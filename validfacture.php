<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();

	$pagetitle = "GSF | Validation des Ventes";
	$pagestitle = "Validation des Ventes"; // A remplacer après
	$bcrumb = "Vente > Validation Vente";
	
	$msg = "";
	$classmsg = "";
	$action = "";
	
	$totvent = 0;
	
	//pagination
	$parpage = 10;
	$sql = "SELECT DISTINCT codeFacture FROM facture WHERE statutFacture=0 AND facture_codeTypeF!='COMPTANT'";
	$nblignes = count(SQLSelect($sql));
	$nbpages = ceil($nblignes/$parpage);
	$tatus = 'CD';
	$objet = 'Règlement Facture crédit';
	
	if(isset($_GET['action']))
	{
		$getaction = $_GET['action'];
		$getcode = $_GET['code'];
		$mtTT = $_GET['tt'];
		
		if($getaction=="valid")
		{
			$stat = 1;
			$valider = $bdd->db->PREPARE("UPDATE facture SET statutFacture=:stat
			WHERE codeFacture=:code");
			$valider->EXECUTE(array('stat'=>$stat,'code'=>$getcode));
			
			$msg = "Vente validée avec succès!";
			$classmsg = "alert alert-success";
			$action = "<br><br><br><a href='validfacture.php'><input type='button' 
			class='btn btn-primary' value='NOUVEAU'></a>";
			// impression après validation
			// header("location:popupbl.php?codefact=$getcode");
			//insertion dans reglement
			$regl = $bdd->db->PREPARE("INSERT INTO reglement (dateReglement,montantReglement,objetReglement,statutReglement,reglement_codeFacture)
			VALUES(:datef,:mtTT,:objet,:status,:codeF)");
			$regl->EXECUTE(array('datef'=>date("Y-m-d"),'mtTT'=>$mtTT,'objet'=>$objet,'status'=>$tatus,'codeF'=>$getcode));
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
		$sqlrech = "SELECT DISTINCT idFacture, facture_codeClient client,codeFacture facture,dateFacture datef,
		SUM(totalFacture) total,remiseFacture remise,SUM(totalFacture)-remiseFacture NAP 
		FROM facture WHERE statutFacture=0 AND facture_codeTypeF!='COMPTANT' GROUP BY codeFacture LIMIT $first, $parpage";
		$factures = SQLSelect($sqlrech);
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
									$ttc = 0 ;
									$tva = 0 ;
									$mttva = getTVA($fact->facture);
									$remise = $fact->remise;
									$ht = $fact->total;
									if($mttva==0){
										$ttc = $ht - $remise;
										$tva = 0 ;
									}else{
										$tva = ($ht*0.18);
										$ttc= $ht + $tva - $remise;
									}
						?>
									<tr>
										<td><?= $numligne++;?></td>
										<td><?= getClient($fact->client); ?></td>
										<td><?= $fact->facture; ?></td>
										<td><?= date_format(date_create($fact->datef),'d/m/Y'); ?></td>
										<td><?= number_format($ht, 0, ',', ' '); ?></td>
										<td><?= number_format($remise, 0, ',', ' '); ?></td>
										<!--<td><?php //echo number_format(($fact->NAP)+(getTVA($fact->facture)), 0, ',', ' '); ?></td>-->
										<td><?= number_format($ttc, 0, ',', ' '); ?></td>
										<td>
											<a onClick="window.open('popupfacture.php?codefact=<?=$fact->facture;?>','','width=1200, height=900, top=30, left=50')" href="validfacture.php?action=valid&code=<?= $fact->facture;?>&id=<?= $fact->idFacture;?>&tt=<?= $ttc;?>">
												<button class='btn bg-green'>VALIDER</button>
											</a>
										</td>
										<td>
											<a href="" onClick="window.open('popupbl.php?codefact=<?=$fact->facture;?>','','width=1200, height=900, top=30, left=50')">
												<button class='btn bg-orange'>Bord. L</button>
											</a>	
											<!--<button class='btn bg-orange' onclick="javascript:popup('<?php //=$fact->facture;?>');">
												Bord. L
											</button>-->
										</td>
									</tr>
						<?php
									// $totvent += $fact->NAP;
									$totvent += $ttc;
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
							<li><a href="validfacture.php?page=<?= $i;?>"><?= $i;?></a></li>
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