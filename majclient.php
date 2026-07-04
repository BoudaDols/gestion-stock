<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();
	
	$pagetitle = "GSF | M.A.J des clients";
	$pagestitle = "Mise à jour des clients"; // A remplacer après
	$bcrumb = "Paramétrage > M.A.J Clients";
	$display = "style='display:none'"; //Sert à afficher/cacher le btn 'annuler la modif'
	
	$btnaction = "insert";
	$disabledc = ""; //Sert à griser le code pour update
	$disabled = ""; //Sert à griser les champs après insert ou update
	$tableau = "entier"; //Différencier l'?age du tableau: si tout le tableau ou une recherche
	$msg = "";
	$classmsg = "";
	$button = "";
	$action = "";
	
	$codeC = "";
	$nomC = "";
	$telC = "";
	$emailC = "";
	$adresseC = "";
	$regimeC = "";
	$divisionC = "";
	$rccmC = "";
	$ifuC = "";
	
	//Pagination
	$parpage = 8;
	$sql = "SELECT * FROM client";
	$nblignes = count(SQLSelect($sql));
	$nbpages = ceil($nblignes/$parpage);
	
	if(isset($_GET['action']))
	{
		$getaction = $_GET['action'];
		$getcode = $_GET['code'];
		
		if($getaction=="edit")
		{
			$sqledit = "SELECT * FROM client WHERE codeClient='$getcode'";
			$edits = SQLSelect($sqledit);
			foreach($edits as $edit):
				$codeC = $getcode;
				$nomC = stripslashes($edit->nomClient);
				$adresseC = $edit->adresseClient;
				$telC = $edit->telClient;
				$emailC =  $edit->emailClient;
				$regimeC = $edit->regimeClient;
				$divisionC = $edit->divisionClient;
				$rccmC = $edit->rccmClient;
				$ifuC = $edit->ifuClient;
			endforeach;
			$btnaction = "update";
			$disabledc = "disabled";
			$display = "style='display:inline'";
		}
		elseif($getaction=="statut")
		{
			$sqlrechstat = "SELECT * FROM client WHERE codeClient='$getcode'";
			$lestats = SQLSelect($sqlrechstat);
			foreach($lestats as $lestat):
				$oldstat = $lestat->statutClient;
			endforeach;
			if($oldstat=="ON")
			{
				$sqlstat = $bdd->db->PREPARE("UPDATE client SET statutClient=:nstat WHERE codeClient=:getcode");
				$sqlstat->EXECUTE(array('nstat'=>'OFF', 'getcode'=>$getcode));
				
				if($sqlstat)
				{
					$msg="Client desactivé!";
					$classmsg = "alert alert-warning";
					$action = "<br><br><br><a href='majclient.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabledc = "disabled";
					$disabled = "disabled";
				}
			}
			else
			{
				$sqlstat = $bdd->db->PREPARE("UPDATE client SET statutClient=:nstat WHERE codeClient=:getcode");
				$sqlstat->EXECUTE(array('nstat'=>'ON', 'getcode'=>$getcode));
				
				if($sqlstat)
				{
					$msg="Client activé!";
					$classmsg = "alert alert-success";
					$action = "<br><br><br><a href='majclient.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabledc = "disabled";
					$disabled = "disabled";
				}
			}
		}	
	}
	
	if(isset($_POST['btnsubmit']))
	{
		$btnaction = $_POST['btnaction'];
		
		if($btnaction=="insert")
		{
			$codeC = $_POST['codeC'];
			$nomC = addslashes($_POST['nomC']);
			$telC = $_POST['telC'];
			$emailC = addslashes($_POST['emailC']);
			$adresseC = addslashes($_POST['adresseC']);
			$regimeC = addslashes($_POST['regimeC']);
			$divisionC = addslashes($_POST['divisionC']);
			$rccmC = addslashes($_POST['rccmC']);
			$ifuC = addslashes($_POST['ifuC']);
			$stat = "ON";
			
			//Vérifier si le même code n'est pas déjà utilisé
			$verifCode = "SELECT * FROM client WHERE codeClient='$codeC'";
			$result = SQLSelect($verifCode);
			if(!empty($result))
			{
				$msg = "Code déjà attribué à un client!<br>Créez-en un autre.";
				$classmsg = "alert alert-info";
				$button = "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";				
			}
			else
			{
				$sql = $bdd->db->PREPARE("INSERT INTO client 
										(codeClient, nomClient, adresseClient, telClient, emailClient, regimeClient,
										rccmClient, ifuClient, divisionClient, statutClient) 
										VALUES(:code, :nom, :adresse, :tel, :email, :regime, :rccm, :ifu, :division, :stat)
										");
				$sql->EXECUTE(array(
									'code' => $codeC, 'nom' => $nomC, 
									'adresse' => $adresseC, 'tel' => $telC, 'email' => $emailC, 'regime'=>$regimeC,
									'rccm'=>$rccmC, 'ifu'=>$ifuC, 'division'=>$divisionC, 'stat' => $stat
									));
				if($sql)
				{
					$msg="Client créé avec succès!";
					$classmsg = "alert alert-success";
					$action = "<br><br><br><a href='majclient.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabledc = "disabled";
					$disabled = "disabled";
				}
				else
				{
					$msg="Erreur lors de la création du client";
					$classmsg = "alert alert-warning";
					$action = "<br><br><br><a href='majclient.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabledc = "disabled";
					$disabled = "disabled";
				}
			}
			
		}
		else
		{
			$nomC = addslashes($_POST['nomC']);
			$telC = $_POST['telC'];
			$emailC = addslashes($_POST['emailC']);
			$adresseC = addslashes($_POST['adresseC']);
			$regimeC = addslashes($_POST['regimeC']);
			$divisionC = addslashes($_POST['divisionC']);
			$rccmC = addslashes($_POST['rccmC']);
			$ifuC = addslashes($_POST['ifuC']);
			
			$sql = $bdd->db->PREPARE("UPDATE client SET nomClient=:nom, adresseClient=:adresse, 
									telClient=:tel, emailClient=:mail, regimeClient=:regime, rccmClient=:rccm, 
									ifuClient=:ifu,divisionClient=:division WHERE codeClient=:getcode");
			$sql->EXECUTE(array('nom'=>$nomC,'adresse'=>$adresseC, 'tel'=>$telC, 'mail'=>$emailC, 
								'regime'=>$regimeC, 'rccm'=>$rccmC, 'ifu'=>$ifuC, 'division'=>$divisionC, 'getcode'=>$getcode));
			if($sql)
			{
				$msg="Client modifié avec succès!";
				$classmsg = "alert alert-success";
				$action = "<br><br><br><a href='majclient.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
				$disabledc = "disabled";
				$disabled = "disabled";
					
				$display = "style='display:none'";
			}
			else
			{
				$msg="Erreur lors de la modification du client";
				$classmsg = "alert alert-warning";
				$action = "<br><br><br><a href='majclient.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
				$disabledc = "disabled";
				$disabled = "disabled";
			}	
		}		
	}
	
	//Navigation pagination
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
	
	
	if(isset($_POST['btnresearch']))
	{
		$rech = $_POST['research'];
		if($rech=="")
		{
			$sqlrech = "SELECT * FROM client LIMIT $first, $parpage";
			$tableau = "entier";
		}
		else
		{
			$sqlrech = "SELECT * FROM client WHERE codeClient LIKE '%$rech%' OR nomClient LIKE '%$rech%' LIMIT $first, $parpage";
			$tableau = "rechercher";
		}
		
	}
	
	ob_start();
?>
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-user"></i>
				<h3 class="box-title">Ajout/Modification Clientèle</h3>
			</div>
			<form name="majclient" method="POST">
				<div class="box-body">
					<div class="row">
						<div class="form-group col-xs-4">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-barcode"></i>
								</div>
								<input type="text" class="form-control" style="width:270px" name="codeC" placeholder="Code du client" value="<?= $codeC;?>" <?= $disabledc; ?> required/>
							</div>
						</div>
						<div class="form-group col-xs-4">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-phone"></i>
								</div>
								<input type="text" class="form-control" style="width:270" name="telC" placeholder="Téléphone du client" value="<?= $telC;?>" <?= $disabled; ?> required/>
							</div>
						</div>
						<div class="form-group col-xs-4">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-globe"></i>
								</div>
								<input type="text" class="form-control" style="width:250px" name="adresseC" placeholder="Adresse du client" value="<?= $adresseC;?>" <?= $disabled; ?> required/>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-xs-4">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-font"></i>
								</div>
								<input type="text" class="form-control" style="width:270px" name="nomC" placeholder="Nom du client" value="<?= $nomC;?>" <?= $disabled; ?> required/>
							</div>
						</div>
						<div class="form-group col-xs-4">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-font"></i>
								</div>
								<input type="email" class="form-control" style="width:270px" name="emailC" placeholder="Mail du client" value="<?= $emailC;?>" <?= $disabled; ?> />
							</div>
						</div>
						<div class="form-group col-xs-4">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-font"></i>
								</div>
								<input type="text" class="form-control" style="width:250px" name="regimeC" placeholder="Régime d'impôt du client" value="<?= $regimeC;?>" <?= $disabled; ?> />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group col-xs-4">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-font"></i>
								</div>
								<input type="text" class="form-control" style="width:270px" name="divisionC" placeholder="Division Fiscale du client" value="<?= $divisionC;?>" <?= $disabled; ?> />
							</div>
						</div>
						<div class="form-group col-xs-4">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-font"></i>
								</div>
								<input type="text" class="form-control" style="width:270px" name="rccmC" placeholder="N° RCCM du client" value="<?= $rccmC;?>" <?= $disabled; ?> />
							</div>
						</div>
						<div class="form-group col-xs-4">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-font"></i>
								</div>
								<input type="text" class="form-control" style="width:250px" name="ifuC" placeholder="N° IFU du client" value="<?= $ifuC;?>" <?= $disabled; ?> />
							</div>
						</div>
					</div>
					<div class="row col-lg-3"></div>
					<div class="row col-lg-3">
						<input type="hidden" name="btnaction" value="<?= $btnaction; ?>">
						<input type="submit" name="btnsubmit" class="btn btn-primary" value="VALIDER" <?= $disabled; ?>>
					</div>
					<div class="row col-lg-3" <?= $display;?> >
						<a href="majclient.php">
							<input type="button" name="btncancel" class="btn btn-info" value="ANNULER MODIFICATION ">
						</a>
					</div>
					<div class="row col-lg-3"></div>
				</div>
			</form>
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
	
	<?php
		$sqlentier = "SELECT * FROM client LIMIT $first, $parpage";
		if($tableau=="entier")
		{
			$clients = SQLSelect($sqlentier);
		}
		else
		{
			$clients = SQLSelect($sqlrech);
		}
	?>
	
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-filter"></i>
				<h3 class="box-title">Liste de la clientèle</h3>
			</div>
			<div class="row">
					<div class="col-lg-4"></div>
					<div class="col-lg-4"></div>
					<div class="col-lg-4">
						<form role="form" class="form-inline" name="rechclient" action="" method="post">
							<input type="text" name="research" placeholder="Code du client" class="form-control">
							<button class="btn btn-info btn-flat" name="btnresearch" type="submit">Lister</button>
						</form>
					</div>
				</div>
			<div class="box-body">
				
				<table class="table table-bordered" name="tabclient" id="tabclient">
					<thead>
						<tr>
							<th style="width:50px">#</th>
							<th style="width:50px">CODE</th>
							<th>NOM</th>
							<th style="width:100px">ADRESSE</th>
							<th style="width:60px">TEL.</th>
							<th style="width:80px">Mail</th>
							<th style="width:80px">N° RCCM</th>
							<th style="width:80px">N° IFU</th>
							<th style="width:90px"></th>
							<th style="width:90px"></th>
						</tr>
					</thead>
					<tbody>
						<?php
							if(empty($clients))
							{
						?>
								<tr>
									<td colspan="11">Aucun client dans la base.</td>
								</tr>
						<?php
							}
							else
							{
								foreach($clients as $client):
						?>
									<tr>
										<td><?= $numligne++;?></td>
										<td><?= $client->codeClient; ?></td>
										<td><?= stripslashes($client->nomClient) ?></td>
										<td><?= $client->adresseClient; ?></td>
										<td><?= $client->telClient; ?></td>
										<td><?= $client->emailClient; ?></td>
										<td><?= $client->rccmClient; ?></td>
										<td><?= $client->ifuClient; ?></td>
										<td>
											<a href="majclient.php?action=edit&code=<?= $client->codeClient; ?>">
												<button class='btn bg-orange'>EDITER</button>
											</a>
										</td>
										<td>
											<a href="majclient.php?action=statut&code=<?= $client->codeClient; ?>">
												<?php
													if($client->statutClient=="ON")
														echo "<button class='btn bg-olive'>DESACT.</button>";
													else
														echo "<button class='btn bg-olive'>ACT.</button>";
												?>
											</a>
										</td>
									</tr>
						<?php
								endforeach;
							}
						?>
					</tbody>
					<tfoot>
						<tr>
								<th style="width:50px">#</th>
							<th style="width:50px">CODE</th>
							<th>NOM</th>
							<th style="width:100px">ADRESSE</th>
							<th style="width:60px">TEL.</th>
							<th style="width:80px">Mail</th>
							<th style="width:80px">N° RCCM</th>
							<th style="width:80px">N° IFU</th>
							<th style="width:90px"></th>
							<th style="width:90px"></th>
						</tr>
					</tfoot>
				</table>
				<br>
				<?php
					if($tableau=="entier")
					{
				?>
					<ul class="pagination pagination-sm no-margin pull-right">
						<?php
							for($i=1; $i<=$nbpages; $i++)
							{
						?>
								<li><a href="majclient.php?page=<?= $i;?>"><?= $i;?></a></li>
				<?php
						}?>
					</ul>	
				<?php	}
				?>
				
			</div>
		</div>
	</div>
	
<?php
	$content = ob_get_clean();
	require_once('patterning.php');
?>