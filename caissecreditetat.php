<?php
require_once('php/session.php');
require_once('php/fonction.php');

$pagetitle = "GSF | Etat des ventes";
$pagestitle = "Etat des ventes";
$bcrumb = "Statistique > Liste des ventes";

//pagination
$parpage = 10;
$countResult = SQLSelect("SELECT * FROM reglement WHERE reglement_codeFacture != '' AND statutReglement = 'CD' ORDER BY dateReglement DESC");
$nblignes = $countResult ? count($countResult) : 0;
$nbpages = ceil($nblignes / $parpage);

// Navigation pagination
if (isset($_GET['page'])) {
    $pactu = intval($_GET['page']);
    if ($pactu > $nbpages) {
        $pactu = $nbpages;
    }
} else {
    $pactu = 1;
}
$numligne = ($pactu * $parpage) - $parpage + 1;
$first = ($pactu - 1) * $parpage;

ob_start();
?>
<?php
	$regl = SQLSelect("SELECT * FROM reglement WHERE reglement_codeFacture != '' AND 
	statutReglement = 'C' ORDER BY dateReglement DESC LIMIT :offset, :limit",
	[':offset' => $first, ':limit' => $parpage]);
?>

	<div class="row col-lg-12">
		<ul class="nav nav-tabs nav-justified">
		 		<li role="presentation" class="active"><a href="caissecomptant.php">Vente au comptant</a></li>
		 	<li role="presentation" class="active"><a href="caissecreditetat.php">Vente à crédit</a></li>
		 	<li role="presentation" class="active"><a href="caisseavoiretat.php">Vente à avoir</a></li>
		</ul>
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-filter"></i>
				<h3 class="box-title">Etat des ventes à crédit</h3>
			</div>
			<div class="box-body">
				<div class="pull-right">
					<button onclick="javascript:popup_vente();" class="btn btn-primary">
						Générer <i class="fa fa-eye"></i>
					</button>
				</div><br /><br />
				<table class="table table-bordered" name="tabvente" id="tabvente">
					<thead>
						<tr>
							<th style="width:50px">#</th>
							<th>DATE</th>
							<th>TYPE</th>
							<th>FACTURE</th>
							<th>MONTANT</th>
						</tr>
					</thead>
					<tbody>
						<?php
							if(empty($regl))
							{
						?>
								<tr>
									<td colspan="4">Aucune vente.</td>
								</tr>
						<?php
							}
							else
							{
								$sum = 0;
								foreach($regl as $reg):
						?>
									<tr>
										<td><?= $numligne++;?></td>
										<td><?= $reg->dateReglement; ?></td>
										<td><?= $reg->objetReglement; ?></td>
										<td><?= $reg->reglement_codeFacture; ?></td>
										<td><?= number_format($reg->montantReglement, 0, ',', ' '); ?></td>
										<td>
											<a href="" onClick="window.open('popupfacture.php?codefact=<?=$reg->reglement_codeFacture;?>','','width=1200, height=900, top=30, left=50')">
												<button class='btn bg-orange'>DETAILS</button>
											</a>
										</td>
									</tr>
						<?php
								$sum += $reg->montantReglement;
								endforeach;
						?>
								<tr>
									<td colspan="4">TOTAL</td>
									<td><?= number_format($sum, 0, ',', ' '); ?></td>
								</tr>
						<?php
							}
						?>
						
					</tbody>
					<tfoot>
						<tr>
							<th style="width:50px">#</th>
							<th>DATE</th>
							<th>CLIENT</th>
							<th>FACTURE</th>
							<th>MONTANT</th>
						</tr>
					</tfoot>
				</table>
				<br>
				<ul class="pagination pagination-sm no-margin pull-right">
					<?php
						for($i=1; $i<=$nbpages; $i++)
						{
					?>
							<li><a href="etatvente.php?page=<?= $i;?>"><?= $i;?></a></li>
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
		
		function popup_vente()
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
			window.open('popup_vente_ens_credit.php','GSF | Vente a credit','menubar=no, scrollbars=no, top='+top+', left='+left+', width='+width+', height='+height+'');
		}
	</script>