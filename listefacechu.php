<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();
	
	$pagetitle = "GSF | Listes des Factures échues";
	$pagestitle = "Factures crédits non soldées échues"; // A remplacer après
	$bcrumb = "Statistique > Factures Crédits Echues";
	
	ob_start();
?>
<?php

	$actu = date_format(date_create(date('Y-m-d')), 'Y-m-d');
	
	$sqlf = "SELECT DISTINCT codeFacture,dateFacture,nomClient,solvabiliteFacture 
	FROM facture,client,article WHERE facture_codeTypeF='CREDIT' AND codeClient=facture_codeClient 
	AND codeArticle=facture_codeArticle AND solvabiliteFacture=0";
	$factures = SQLSelect($sqlf);
	$tabech = array();
	foreach($factures as $fact):
		$next = getNextDate($fact->codeFacture);
		if(strtotime($actu)>=strtotime($next))
		{
			$ttc = getTTC($fact->codeFacture);
			$vers = getSumPaidC($fact->codeFacture);
			$solde = $ttc - $vers;
			$tabech[] = array('codef'=>$fact->codeFacture,'clientf'=>$fact->nomClient,
			'datef'=>$fact->dateFacture,'total'=>$ttc,'versf'=>$vers,'solde'=>$solde);
		}
	endforeach;
?>

	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-filter"></i>
				<h3 class="box-title">Liste des factures crédits non soldées échues</h3>
			</div>
			<div class="box-body">
				<div class="pull-right">
					<button onclick="javascript:popup_facechu();" class="btn btn-primary">
						Générer <i class="fa fa-eye"></i>
					</button>
				</div><br /><br />
				<table class="table table-bordered" name="tabfechu" id="tabfechu">
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
							if(count($tabech)<=0)
							{
						?>
								<tr>
									<td colspan="7">Aucune facture crédit.</td>
								</tr>
						<?php
							}
							else
							{
								$tsolde = 0;
								$nl = 1;
								foreach($tabech as $fac):
						?>
									<tr>
										<td><?= $nl++;?></td>
										<td><?= $fac['clientf']; ?></td>
										<td><?= $fac['codef']; ?></td>
										<td><?= date_format(date_create($fac['datef']),'d/m/Y'); ?></td>
										<td><?= number_format($fac['total'], 0, ',', ' '); ?></td>
										<td><?= number_format($fac['versf'], 0, ',', ' '); ?></td>
										<td><?= number_format($fac['solde'], 0, ',', ' '); ?></td>
									</tr>
						<?php
								$tsolde += $fac['solde'];
								endforeach;
						?>
								<tr>
									<td colspan="6">SOLDE TOTAL</td>
									<td><?= number_format($tsolde, 0, ',', ' '); ?></td>
								</tr>
						<?php
							}
						?>
						
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
			</div>
		</div>
	</div>

<?php
	$content = ob_get_clean();
	require_once('patterning.php');
?>

	<script type="text/javascript">
		
		function popup_facechu()
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
			window.open('popup_facechu.php','GSF | Factures échues','menubar=no, scrollbars=no, top='+top+', left='+left+', width='+width+', height='+height+'');
		}
	</script>