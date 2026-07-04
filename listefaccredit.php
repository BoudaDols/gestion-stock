<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();
	
	$pagetitle = "GSF | Listes des Factures crédits";
	$pagestitle = "Factures crédits non soldées"; // A remplacer après
	$bcrumb = "Statistique > Factures Crédits";
	
	//pagination
	$parpage = 5;
	$sql = "SELECT DISTINCT codeFacture,dateFacture,nomClient,solvabiliteFacture 
	FROM facture,client,article WHERE facture_codeTypeF='CREDIT' AND codeClient=facture_codeClient 
	AND codeArticle=facture_codeArticle AND solvabiliteFacture=0";
	$nblignes = count(SQLSelect($sql));
	$nbpages = ceil($nblignes/$parpage);
	
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
<?php
	$sqlf = "SELECT DISTINCT codeFacture,dateFacture,nomClient,solvabiliteFacture 
	FROM facture,client,article WHERE facture_codeTypeF='CREDIT' AND codeClient=facture_codeClient 
	AND codeArticle=facture_codeArticle AND solvabiliteFacture=0 LIMIT $first, $parpage";
	$factures = SQLSelect($sqlf);
?>

	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-filter"></i>
				<h3 class="box-title">Liste des factures crédits non soldées</h3>
			</div>
			<div class="box-body">
				<div class="pull-right">
					<button onclick="javascript:popup_fcredit();" class="btn btn-primary">
						Générer <i class="fa fa-eye"></i>
					</button>
				</div><br /><br />
				<table class="table table-bordered" name="tabfcredit" id="tabfcredit">
					<thead>
						<tr>
							<th style="width:50px">#</th>
							<th>CLIENT</th>
							<th style="width:100px">FACTURE</th>
							<th style="width:50px">DATE</th>
							<th style="width:100px">TOTAL</th>
							<th style="width:100px">TOT. VERSE</th>
							<th style="width:100px">SOLDE</th>
						</tr>
					</thead>
					<tbody>
						<?php
							if(empty($factures))
							{
						?>
								<tr>
									<td colspan="7">Aucune facture crédit.</td>
								</tr>
						<?php
							}
							else
							{
								$solde = 0;
								foreach($factures as $fact):
						?>
									<tr>
										<td><?= $numligne++;?></td>
										<td><?= $fact->nomClient; ?></td>
										<td><?= $fact->codeFacture; ?></td>
										<td><?= date_format(date_create($fact->dateFacture),'d/m/Y'); ?></td>
										<td><?= number_format(getTTC($fact->codeFacture), 0, ',', ' '); ?></td>
										<td><?= number_format(getSumPaidC($fact->codeFacture), 0, ',', ' '); ?></td>
										<td><?= number_format((getTTC($fact->codeFacture)-getSumPaidC($fact->codeFacture)), 0, ',', ' '); ?></td>
									</tr>
						<?php
								$solde += (getTTC($fact->codeFacture)-getSumPaidC($fact->codeFacture));
								endforeach;
							}
						?>
						<tr>
							<td colspan="6">SOLDE TOTAL</td>
							<td><?= number_format($solde, 0, ',', ' '); ?></td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<th style="width:50px">#</th>
							<th>CLIENT</th>
							<th style="width:100px">FACTURE</th>
							<th style="width:50px">DATE</th>
							<th style="width:100px">TOTAL</th>
							<th style="width:100px">TOT. VERSE</th>
							<th style="width:100px">SOLDE</th>
						</tr>
					</tfoot>
				</table>
				<br>
				<ul class="pagination pagination-sm no-margin pull-right">
					<?php
						for($i=1; $i<=$nbpages; $i++)
						{
					?>
							<li><a href="listefaccredit.php?page=<?= $i;?>"><?= $i;?></a></li>
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
		
		function popup_fcredit()
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
			window.open('popup_fcredit.php','GSF | Factures crédits','menubar=no, scrollbars=no, top='+top+', left='+left+', width='+width+', height='+height+'');
		}
	</script>