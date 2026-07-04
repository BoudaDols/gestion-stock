<?php
	require_once('php/session.php');
	require_once('php/fonction.php');

	$pagetitle = "GSF | Livraison Avoir";
	$pagestitle = "Livraison Avoir"; // A remplacer après
	$bcrumb = "Stock > Livraison Avoir";
	
	$msg = "";
	$classmsg = "";
	$action = "";
	
	$totvent = 0;
	
	//pagination
	$parpage = 10;
	$result = SQLSelect("SELECT DISTINCT codeFacture FROM facture WHERE statutFacture=1 AND solvabiliteFacture=1 AND 
	facture_codeModalite='AVOIR' AND facture_codeTypeF='AVOIR'");
	$nblignes = $result ? count($result) : 0;
	$nbpages = ceil($nblignes/$parpage);
	
	if(isset($_GET['action']))
	{
		$getaction = $_GET['action'];
		$getcode = $_GET['code'];
		$mtTT =  $_GET['nap'];
		$objet = "Règlement Facture avoir";
		$tatus = 'A';
		
		if($getaction=="livrer")
		{
			$facts = SQLSelect("SELECT * FROM facture WHERE codeFacture = :code", [':code' => $getcode]);
			
			$stat = 2;
			if(!empty($facts)) {
				foreach($facts as $fact):
					$art = $fact->facture_codeArticle;
					$qtev = $fact->quantiteAFacture;
					$qte = getQte($art) - $qtev;
					
					SQLExecute("UPDATE article SET qteStockArticle = :nqte WHERE codeArticle = :codeart", 
						[':nqte' => $qte, ':codeart' => $art]);
					
					SQLExecute("UPDATE facture SET statutFacture = :stat WHERE codeFacture = :codefact", 
						[':stat' => $stat, ':codefact' => $getcode]);
				endforeach;
			}

			//Reglement ok
			SQLExecute("INSERT INTO reglement (dateReglement,montantReglement,objetReglement,statutReglement,reglement_codeFacture)
			VALUES(:datef,:mtTT,:objet,:status,:codeF)", 
				[':datef' => date("Y-m-d"), ':mtTT' => $mtTT, ':objet' => $objet, ':status' => $tatus, ':codeF' => $getcode]);
			
			$msg = "Livraison effectuée avec succès!";
			$classmsg = "alert alert-success";
			$action = "<br><br><br><a href='stockavoir.php'><input type='button' 
			class='btn btn-primary' value='NOUVEAU'></a>";
		}
	}
	
	// Navigation pagination
	if(isset($_GET['page']))
	{
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
		$factures = SQLSelect("SELECT DISTINCT idFacture, facture_codeClient client,codeFacture facture,dateFacture datef,
		SUM(totalFacture) total,remiseFacture remise,SUM(totalFacture)-remiseFacture NAP 
		FROM facture WHERE statutFacture=1 AND solvabiliteFacture=1 AND 
		facture_codeModalite='AVOIR' AND facture_codeTypeF='AVOIR' GROUP BY codeFacture LIMIT :offset, :limit", 
		[':offset' => $first, ':limit' => $parpage]);
	?>
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-filter"></i>
				<h3 class="box-title">Factures AVOIR en cours</h3>
			</div>
			<div class="box-body">
				<table class="table table-bordered" name="tabvente" id="tabvente">
					<thead>
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
					</thead>
					<tbody>
						<?php
							if(empty($factures))
							{
						?>
								<tr>
									<td colspan="9">Aucune facture AVOIR en cours.</td>
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
										<a href="stockavoir.php?action=livrer&code=<?= $fact->facture;?>&id=<?= $fact->idFacture;?>&nap=<?= $fact->NAP;?>">
												<button class='btn bg-green'>LIVRER</button>
											</a>
										</td>
										<td>
											<button class='btn bg-orange' onclick="javascript:popup('<?=$fact->facture;?>');">
												Bord. L
											</button>
										</td>
									</tr>
						<?php
									$totvent += $fact->NAP;
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
							<li><a href="stockavoir.php?page=<?= $i;?>"><?= $i;?></a></li>
					<?php
						}
					?>
				</ul>
			</div>
		</div>
	</div>


<?php
	$content = ob_get_clean();
	require_once('patterning.php');
?>

	<script type="text/javascript">
		<!--
		function popup(factCode)
		{
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
			window.open('popupbl.php?codefact='+factCode,'GSF | Vente à valider BL','menubar=no, scrollbars=no, top='+top+', left='+left+', width='+width+', height='+height+'');
			window.open('popupfacture.php?codefact='+factCode,'GSF | Vente à valider FAC','menubar=no, scrollbars=no, top='+top+', left='+left+', width='+width+', height='+height+'');
		}
		-->
	</script>
