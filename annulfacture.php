<?php
require_once('php/session.php');
require_once('php/fonction.php');

$pagetitle = "GSF | Annulation des Ventes";
$pagestitle = "Annulation des Ventes";
$bcrumb = "Vente > Annulation Vente";

$msg = "";
$classmsg = "";
$action = "";
							
$totvent = 0;

//pagination
$parpage = 10;
$sqlCount = "SELECT DISTINCT codeFacture FROM facture WHERE statutFacture = 0";
$countResult = SQLSelect($sqlCount);
$nblignes = $countResult ? count($countResult) : 0;
$nbpages = ceil($nblignes / $parpage);

if (isset($_GET['action'])) {
    $getaction = $_GET['action'];
    $getcode = $_GET['code'];

    if ($getaction == "annul") {
        $facts = SQLSelect("SELECT * FROM facture WHERE codeFacture = :code", [':code' => $getcode]);

        // maj la qté en stock des articles concernés par la facture
        if ($facts) {
            foreach ($facts as $fact) {
                $art = $fact->facture_codeArticle;
                $aqte = getQte($art);
                SQLExecute("UPDATE article SET qteStockArticle = :nqte WHERE codeArticle = :codea", [
                    ':nqte' => $fact->quantiteAFacture + $aqte,
                    ':codea' => $fact->facture_codeArticle
                ]);
            }
        }

        // copier le contenu de la facture à annuler dans la table annuler_facture
        SQLExecute("INSERT INTO annuler_facture SELECT * FROM facture WHERE codeFacture = :codefact", [
            ':codefact' => $getcode
        ]);

        // supprimer la facture annulée dans la table facture
        SQLExecute("DELETE FROM facture WHERE codeFacture = :codefac", [
            ':codefac' => $getcode
        ]);

        $msg = "Vente annulée!";
        $classmsg = "alert alert-success";
        $action = "<br><br><br><a href='annulfacture.php'><input type='button' 
        class='btn btn-primary' value='NOUVEAU'></a>";
    }
}

// Navigation pagination
if (isset($_GET['page'])) {
    $pactu = intval($_GET['page']);
		if($pactu > $nbpages)
		{
			$pactu = $nbpages;
		}
	}
	else
	{
		$pactu = 1;
	}
	$numligne = ($pactu*$parpage)-$parpage+1;	
	$first = ($pactu-1)*$parpage;
	
	ob_start();
?>
	
	<div class="row">
		<div class="col-lg-3"></div>
		<div class="col-lg-6">
			<div class="<?=$classmsg; ?>" role="alert">
				<?=$msg; ?>
				<?=$action; ?>
			</div>
		</div>
		<div class="col-lg-3"></div>
	</div>

	<!-- tableau-->
	<?php
		$sqlrech = "SELECT DISTINCT idFacture, facture_codeClient client, codeFacture facture, dateFacture datef,
		SUM(totalFacture) total, remiseFacture remise, SUM(totalFacture)-remiseFacture NAP 
		FROM facture WHERE statutFacture = 0 GROUP BY codeFacture LIMIT :offset, :limit";
		$factures = SQLSelect($sqlrech, [':offset' => $first, ':limit' => $parpage]);
	?>
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-filter"></i>
				<h3 class="box-title">Ventes en cours</h3>
			</div>
			<div class="box-body">
				<table class="table table-bordered" name="tabvente" id="tabvente">
					<thead>
						<tr>
							<th style="width:50px">#</th>
							<th>CLIENT</th>
							<th style="width:100px">FACTURE</th>
							<th style="width:50px">DATE</th>
							<th style="width:100px">TOTAL HT</th>
							<th style="width:100px">REMISE</th>
							<th style="width:100px">TOTAL TTC</th>
							<th style="width:80px"></th>
							<th style="width:80px"></th>
						</tr>
					</thead>
					<tbody>
						<?php
							if(empty($factures))
							{
						?>
								<tr>
									<td colspan="9">Aucune vente en cours.</td>
								</tr>
						<?php
							}
							else
							{
								foreach($factures as $fact):
						?>
									<tr>
										<td><?= $numligne++;?></td>
										<td><?= getClient($fact->client); ?></td>
										<td><?= $fact->facture; ?></td>
										<td><?= date_format(date_create($fact->datef),'d/m/Y'); ?></td>
										<td><?= number_format($fact->total, 0, ',', ' '); ?></td>
										<td><?= number_format($fact->remise, 0, ',', ' '); ?></td>
										<td><?= number_format($fact->NAP, 0, ',', ' '); ?></td>
										<td>
											<a href="annulfacture.php?action=annul&code=<?= $fact->facture;?>&id=<?= $fact->idFacture;?>">
												<button class='btn bg-green'>ANNULER</button>
											</a>
										</td>
										<td>
											<button class='btn bg-orange' onclick="javascript:popup('<?=$fact->facture;?>');">
												Bord. L
											</button>
										</td>
									</tr>
						<?php
									// $totvent += $fact->NAP;
									$totvent += getTTC($fact->facture);
								endforeach;
							}
						?>
						<tr>
							<td style="color:red" colspan="7">
								<b>TOTAL EN COURS A ENCAISSER</b>
							</td>
							<td style="color:red" colspan="2">
								<b><?= number_format($totvent, 0, ',', ' '); ?></b>
							</td>				
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<th style="width:50px">#</th>
							<th>CLIENT</th>
							<th style="width:100px">FACTURE</th>
							<th style="width:50px">DATE</th>
							<th style="width:100px">TOTAL</th>
							<th style="width:100px">REMISE</th>
							<th style="width:100px">NET A PAYER</th>
							<th style="width:80px"></th>
							<th style="width:80px"></th>
						</tr>
					</tfoot>
				</table>
				<br>
				<ul class="pagination pagination-sm no-margin pull-right">
					<?php
						for($i=1; $i<=$nbpages; $i++)
						{
					?>
							<li><a href="annulfacture.php?page=<?= $i;?>"><?= $i;?></a></li>
					<?php
						}
					?>
			</div>
		</div>
	</div>


<?php
	$content = ob_get_clean();
	require_once('patterning.php');
?>

	<script type="text/javascript">
		<!--
		function popup()
		{
			var fact = "<?=$fact->facture;?>";
			width = 1200;
			height = 800;
			if(window.innerWidth)
			{
				var left = (window.innerWidth-width)/2;
				var top = (window.innerHeight-height)/2;
			}
			else
			{
				var left = (document.body.clientWidth-width)/2;
				var top = (document.body.clientHeight-height)/2;
			}
			window.open('popupbl.php?codefact=<?=$fact->facture;?>','GSF | Vente à valider','menubar=no, scrollbars=no, top='+top+', left='+left+', width='+width+', height='+height+'');
		}
		-->
	</script>