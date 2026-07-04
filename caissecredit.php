<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();

	$pagetitle = "GSF | Encaissement des Factures";
	$pagestitle = "Encaissement des factures"; // A remplacer après
	$bcrumb = "Caisse > Facture Crédit";
	
	$msg = "";
	$classmsg = "";
	$action = "";
	$button = "";
	$btnvalue = "ANNULER";
	$display = "style='display:none'";
	
	$totaenc = 0;
	$resteap = 0;
	
	//pagination
	$parpage = 10;
	$sql = "SELECT DISTINCT codeFacture FROM facture WHERE statutFacture=1 AND 
	solvabiliteFacture=0 AND facture_codeTypeF='CREDIT'";
	$nblignes = count(SQLSelect($sql));
	$nbpages = ceil($nblignes/$parpage);
	
	if(isset($_GET['action']))
	{
		$getaction = $_GET['action'];
		$getcode = $_GET['code'];
		
		if($getaction=="encaiss")
		{
			$display = "style='display:inline'"; 
			
			$solv = 1;
			$sum = 0;
			$ttc = 0;
			$remise = getRemise($getcode);
			$brut = getBrut($getcode);
			$net = getBrut($getcode);
			$tva = getTVA($getcode);
			$paye = getSumPaidC($getcode);
			if ($tva == 0) {
				$ttc = $net - $remise;
			}else{
				$rtva=($net*0.18);
				$ttc = $net + $rtva - $remise;
			}
			$resteap=$ttc-$paye;
			$nbtranche = Count(SQLSelect("SELECT * FROM reglement WHERE reglement_codeFacture='$getcode'"));
			// echo 'brut: '.$brut.' - remise: '.$remise.' = net: '.$net.'<br>'; 
			// echo 'Déjà payé: '.getSumPaidC($getcode); 
			
		}
	}
	
	if(isset($_POST['btnsubmit']))
	{
		$versement = $_POST['mtt'];
		$reste = $_POST['resteapayer'];
		$fact = $_POST['codefacture'];
		if(!is_Numeric($versement))
		{
			$msg = "Vérifiez la saisie du versement!";
			$classmsg = "alert alert-danger";
			$button = "<button type='button' class='close' data-dismiss='alert' 
			aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";
		}
		else
		{
			if($versement>$resteap)
			{
				$msg = "Le montant saisi est supérieur au reste à payer!";
				$classmsg = "alert alert-warning";
				$button = "<button type='button' class='close' data-dismiss='alert' 
				aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";
			}
			else
			{
				$insertreg = $bdd->db->PREPARE("INSERT INTO reglement (dateReglement,montantReglement,
				objetReglement,statutReglement,reglement_codeFacture)
				VALUES(:date,:mtt,:obj,:stat,:fac)");
				$insertreg->EXECUTE(array('date'=>date("Y-m-d"),'mtt'=>$versement,'obj'=>'Règlement Facture crédit',
				'stat'=>'C','fac'=>$fact));
				
				//controler si facture est à solder
				if(($versement-$reste)>=0)
				{
					$updatefac = $bdd->db->PREPARE("UPDATE facture SET solvabiliteFacture=:sold
					WHERE codeFacture=:codefac");
					$updatefac->EXECUTE(array('sold'=>1,'codefac'=>$fact));
				}
				
				/*$display = "style='display:none'";
				$msg = "Facture encaissée avec succès!";
				$classmsg = "alert alert-success";
				$action = "<br><br><br><a href='caissecredit.php'><input type='button' 
				class='btn btn-primary' value='NOUVEAU'></a>";*/
				// impression après encaissement
				// header("location:popupcredit.php?codefact=$getcode");
				$btnvalue = "RETOUR";
				echo "<script type='text/javascript' language='javascript'>window.open('popupcredit.php?codefact=$getcode'); </script>";
			}
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
	
	<div class="row col-lg-12" <?=$display;?>>
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-money"></i>
				<h3 class="box-title">Encaissement Factures CREDIT</h3>
			</div>
			<div class="box-body">
				<form name="caissecredit" method="POST">
					
					<div class="row col-lg-3">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-eye"></i>
								</div>
								<input type="text" class="form-control" style="width:130px" name="tranche" value="Versement N° <?=$nbtranche+1;?>" disabled="disabled"/>
							</div>
						</div>
					</div>
					<div class="row col-lg-3">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-plus"></i>
								</div>
								<input type="text" class="form-control" style="width:160px" name="net" value="Total: <?=$ttc;?>" disabled="disabled"/>
							</div>
						</div>
					</div>
					<div class="row col-lg-3">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-minus"></i>
								</div>
								<input type="text" class="form-control" style="width:160px" name="reste" value="Reste: <?=$resteap?>" disabled="disabled"/>
							</div>
						</div>
					</div>
					<div class="row col-lg-3">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-money"></i>
								</div>
								<input type="text" class="form-control" style="width:160px" name="mtt" placeholder="Montant à encaisser" required/>
							</div>
						</div>
					</div>
					
					<input type="hidden" class="form-control" style="width:160px" name="resteapayer" value="<?=$net-$paye;?>" />
					<input type="hidden" class="form-control" style="width:160px" name="codefacture" value="<?=$getcode;?>" />
					
					<div class="row col-lg-3"></div>
					<div class="row col-lg-3">
						<input type="submit" name="btnsubmit" class="btn btn-primary" value="VALIDER">
					</div>
					<div class="row col-lg-3">
						<a href="caissecredit.php"><input type="button"class="btn btn-info" value="<?=$btnvalue;?>"></a>
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-lg-3"></div>
		<div class="col-lg-6">
			<div class="<?=$classmsg; ?>" role="alert">
				<?=$button; ?>
				<?=$msg; ?>
				<?=$action; ?>
			</div>
		</div>
		<div class="col-lg-3"></div>
	</div>

	<!-- tableau-->
	<?php
		$sqlrech = "SELECT DISTINCT idFacture,facture_codeClient client,codeFacture facture,dateFacture datef,
		SUM(totalFacture) total,remiseFacture remise,tvaFacture itva 
		FROM facture WHERE statutFacture=1 AND solvabiliteFacture=0 
		AND facture_codeTypeF='CREDIT' 
		GROUP BY codeFacture LIMIT $first, $parpage";
		$factures = SQLSelect($sqlrech);
	?>
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-filter"></i>
				<h3 class="box-title">Ventes validées</h3>
			</div>
			<div class="box-body">
				<table class="table table-bordered" name="tabvente" id="tabvente">
					<thead>
						<tr>
							<th style="width:50px">#</th>
							<th>CLIENT</th>
							<th style="width:100px">FACTURE</th>
							<th style="width:50px">DATE</th>
							<th style="width:100px">TOTAL TTC</th>
							<th style="width:100px">SOLDE</th>
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
									<td colspan="8">Aucune vente validée.</td>
								</tr>
						<?php
							}
							else
							{
								foreach($factures as $fact):
									$solde=0;
									$totttc = 0;
									if ($fact->itva == 0) {
										$totttc = ($fact->total) - ($fact->remise);
									}else{
										$mttva=($fact->total)*0.18;
										$totttc = ($fact->total) + $mttva - ($fact->remise);
									}
									$solde = $totttc - getSumPaidC($fact->facture);
									

						?>
									<tr>
										<td><?= $numligne++;?></td>
										<td><?= getClient($fact->client); ?></td>
										<td><?= $fact->facture; ?></td>
										<td><?= date_format(date_create($fact->datef),'d/m/Y'); ?></td>
										<td><?= number_format($totttc, 0, ',', ' '); ?></td>
										<td><?= number_format($solde, 0, ',', ' '); ?></td>
										<td>
											<a href="caissecredit.php?action=encaiss&code=<?= $fact->facture;?>&id=<?= $fact->idFacture;?>">
												<button class='btn bg-green'>ENCAISSER</button>
											</a>
										</td>
										<td>
											<a href="" onClick="window.open('popupfacture.php?codefact=<?=$fact->facture;?>','','width=1200, height=900, top=30, left=50')">
												<button class='btn bg-orange'>DETAILS</button>
											</a>
											<!--<button class='btn bg-orange' onclick="javascript:popup('<?php//=$fact->facture;?>');">
												DETAILS
											</button>-->
										</td>
									</tr>
						<?php
									// $totaenc += getNet($fact->facture)-getSumPaidC($fact->facture);
									$totaenc += $solde;
								endforeach;
							}
						?>
						<tr>
							<td style="color:red" colspan="5">
								<b>TOTAL EN COURS A ENCAISSER</b>
							</td>
							<td style="color:red" colspan="3">
								<b><?= number_format($totaenc, 0, ',', ' '); ?></b>
							</td>				
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<th style="width:50px">#</th>
							<th>CLIENT</th>
							<th style="width:100px">FACTURE</th>
							<th style="width:50px">DATE</th>
							<th style="width:100px">TOTAL TTC</th>
							<th style="width:100px">SOLDE</th>
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
							<li><a href="caissecredit.php?page=<?= $i;?>"><?= $i;?></a></li>
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