<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();
	
	$pagetitle = "GSF | M.A.J des fournisseurs";
	$pagestitle = "Mise à jour des fournisseurs"; // A remplacer après
	$bcrumb = "Paramétrage > M.A.J Fournisseurs";
	$display = "style='display:none'"; //Sert à afficher/cacher le btn 'annuler la modif'
	
	$btnaction = "insert";
	$disabledc = ""; //Sert à griser le code pour update
	$disabled = ""; //Sert à griser les champs après insert ou update
	$tableau = "entier"; //Différencier l'?age du tableau: si tout le tableau ou une recherche
	$msg = "";
	$classmsg = "";
	$button = "";
	$action = "";
	
	$codeF = "";
	$nomF = "";
	$telF = "";
	$adresseF = "";
	
	//Pagination
	$parpage = 10;
	$sql = "SELECT * FROM fournisseur";
	$nblignes = count(SQLSelect($sql));
	$nbpages = ceil($nblignes/$parpage);
	
	if(isset($_GET['action']))
	{
		$getaction = $_GET['action'];
		$getcode = $_GET['code'];
		
		if($getaction=="edit")
		{
			$sqledit = "SELECT * FROM fournisseur WHERE codeFournisseur='$getcode'";
			$edits = SQLSelect($sqledit);
			foreach($edits as $edit):
				$codeF = $getcode;
				$nomF = stripslashes($edit->nomFournisseur);
				$adresseF = $edit->adresseFournisseur;
				$telF = $edit->telFournisseur;
			endforeach;
			$btnaction = "update";
			$disabledc = "disabled";
			$display = "style='display:inline'";
		}
		elseif($getaction=="statut")
		{
			$sqlrechstat = "SELECT * FROM fournisseur WHERE codeFournisseur='$getcode'";
			$lestats = SQLSelect($sqlrechstat);
			foreach($lestats as $lestat):
				$oldstat = $lestat->statutFournisseur;
			endforeach;
			if($oldstat=="ON")
			{
				$sqlstat = $bdd->db->PREPARE("UPDATE fournisseur SET statutFournisseur=:nstat WHERE codeFournisseur=:getcode");
				$sqlstat->EXECUTE(array('nstat'=>'OFF', 'getcode'=>$getcode));
				
				if($sqlstat)
				{
					$msg="Fournisseur desactivé!";
					$classmsg = "alert alert-warning";
					$action = "<br><br><br><a href='majfournisseur.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabledc = "disabled";
					$disabled = "disabled";
				}
				
			}
			else
			{
				$sqlstat = $bdd->db->PREPARE("UPDATE fournisseur SET statutFournisseur=:nstat WHERE codeFournisseur=:getcode");
				$sqlstat->EXECUTE(array('nstat'=>'ON', 'getcode'=>$getcode));
				
				if($sqlstat)
				{
					$msg="Fournisseur activé!";
					$classmsg = "alert alert-success";
					$action = "<br><br><br><a href='majfournisseur.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
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
			
			$codeF = $_POST['codeF'];
			$nomF = addslashes($_POST['nomF']);
			$telF = $_POST['telF'];
			$adresseF = addslashes($_POST['adresseF']);
			$stat = "ON";
			
			//Vérifier si le même code n'est pas déjà utilisé
			$verifCode = "SELECT * FROM fournisseur WHERE codeFournisseur='$codeF'";
			$result = SQLSelect($verifCode);
			if(!empty($result))
			{
				$msg = "Code déjà attribué à un fournisseur!<br>Créez-en un autre.";
				$classmsg = "alert alert-info";
				$button = "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";				
			}
			else
			{
				$sql = $bdd->db->PREPARE("INSERT INTO fournisseur 
										(codeFournisseur, nomFournisseur, adresseFournisseur, telFournisseur, statutFournisseur) 
										VALUES(:code, :nom, :adresse, :tel, :stat)
										");
				$sql->EXECUTE(array(
									'code' => $codeF, 'nom' => $nomF, 
									'adresse' => $adresseF, 'tel' => $telF, 'stat' => $stat
									));
				if($sql)
				{
					$msg="Fournisseur créé avec succès!";
					$classmsg = "alert alert-success";
					$action = "<br><br><br><a href='majfournisseur.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabledc = "disabled";
					$disabled = "disabled";
				}
				else
				{
					$msg="Erreur lors de la création du fournisseur";
					$classmsg = "alert alert-warning";
					$button = "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";
					
					$codeF = $_POST['codeF'];
					$nomF = addslashes($_POST['nomF']);
					$telF = $_POST['telF'];
					$adresseF = addslashes($_POST['adresseF']);
				}
			}
			
		}
		else
		{
			$nomF = addslashes($_POST['nomF']);
			$telF = $_POST['telF'];
			$adresseF = addslashes($_POST['adresseF']);
			
			$sql = $bdd->db->PREPARE("UPDATE fournisseur SET nomFournisseur=:nom, 
									adresseFournisseur=:adresse, telFournisseur=:tel
									WHERE codeFournisseur=:getcode");
			$sql->EXECUTE(array('nom'=>$nomF,'adresse'=>$adresseF, 
							 'tel'=>$telF,'getcode'=>$getcode));
			if($sql)
			{
				$msg="Fournisseur modifié avec succès!";
				$classmsg = "alert alert-success";
				$action = "<br><br><br><a href='majfournisseur.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
				$disabledc = "disabled";
				$disabled = "disabled";
				
				$display = "style='display:none'";
			}
			else
			{
				$msg="Erreur lors de la modification du fournisseur";
				$classmsg = "alert alert-warning";
				$action = "<br><br><br><a href='majfournisseur.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
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
			$sqlrech = "SELECT * FROM fournisseur LIMIT $first, $parpage";
			$tableau = "entier";
		}
		else
		{
			$sqlrech = "SELECT * FROM fournisseur WHERE codeFournisseur LIKE '%$rech%' OR nomFournisseur LIKE '%$rech%' LIMIT $first, $parpage";
			$tableau = "rechercher";
		}
		
	}
	
	ob_start();
?>
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-truck"></i>
				<h3 class="box-title">Ajout/Modification Fournisseur</h3>
			</div>
			<form name="majclient" method="POST">
				<div class="box-body">
					<div class="row col-lg-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-barcode"></i>
								</div>
								<input type="text" class="form-control" style="width:350px" name="codeF" placeholder="Code du fournisseur" value="<?= $codeF;?>" <?= $disabledc; ?> required/>
							</div>
						</div>
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-font"></i>
								</div>
								<input type="text" class="form-control" style="width:350px" name="nomF" placeholder="Nom du fournisseur" value="<?= $nomF;?>" <?= $disabled; ?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-phone"></i>
								</div>
								<input type="text" class="form-control" style="width:350px" name="telF" placeholder="Téléphone du fournisseur" value="<?= $telF;?>" <?= $disabled; ?> required/>
							</div>
						</div>
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-globe"></i>
								</div>
								<input type="text" class="form-control" style="width:350px" name="adresseF" placeholder="Adresse du fournisseur" value="<?= $adresseF;?>" <?= $disabled; ?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-3"></div>
					<div class="row col-lg-3">
						<input type="hidden" name="btnaction" value="<?= $btnaction; ?>">
						<input type="submit" name="btnsubmit" class="btn btn-primary" value="VALIDER" <?= $disabled; ?>>
					</div>
					<div class="row col-lg-3" <?= $display;?> >
						<a href="majfournisseur.php">
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
		$sqlentier = "SELECT * FROM fournisseur LIMIT $first, $parpage";
		if($tableau=="entier")
		{
			$fournisseurs = SQLSelect($sqlentier);
		}
		else
		{
			$fournisseurs = SQLSelect($sqlrech);
		}
	?>
	
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-filter"></i>
				<h3 class="box-title">Liste des fournisseurs</h3>
			</div>
			<div class="row">
					<div class="col-lg-4"></div>
					<div class="col-lg-4"></div>
					<div class="col-lg-4">
						<form role="form" class="form-inline" name="rechclient" action="" method="post">
							<input type="text" name="research" placeholder="Code/Nom du fournisseur" class="form-control">
							<button class="btn btn-info btn-flat" name="btnresearch" type="submit">Lister</button>
						</form>
					</div>
				</div>
			<div class="box-body">
				
				<table class="table table-bordered" name="tabfrs" id="tabclient">
					<thead>
						<tr>
							<th style="width:50px">#</th>
							<th style="width:50px">CODE</th>
							<th>NOM</th>
							<th style="width:100px">ADRESSE</th>
							<th style="width:100px">TELEPHONE</th>
							<th style="width:100px"></th>
							<th style="width:100px"></th>
						</tr>
					</thead>
					<tbody>
						<?php
							if(empty($fournisseurs))
							{
						?>
								<tr>
									<td colspan="7">Aucun fournisseur dans la base.</td>
								</tr>
						<?php
							}
							else
							{
								foreach($fournisseurs as $frs):
						?>
									<tr>
										<td><?= $numligne++;?></td>
										<td><?= $frs->codeFournisseur; ?></td>
										<td><?= stripslashes($frs->nomFournisseur) ?></td>
										<td><?= $frs->adresseFournisseur; ?></td>
										<td><?= $frs->telFournisseur; ?></td>
										<td>
											<a href="majfournisseur.php?action=edit&code=<?= $frs->codeFournisseur; ?>">
												<button class='btn bg-orange'>EDITER</button>
											</a>
										</td>
										<td>
											<a href="majfournisseur.php?action=statut&code=<?= $frs->codeFournisseur; ?>">
												<?php
													if($frs->statutFournisseur=="ON")
														echo "<button class='btn bg-olive'>DESACTIVER</button>";
													else
														echo "<button class='btn bg-olive'>ACTIVER</button>";
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
							<th>#</th>
							<th>CODE</th>
							<th>NOM</th>
							<th>ADRESSE</th>
							<th>TELEPHONE</th>
							<th></th>
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
								<li><a href="majfournisseur.php?page=<?= $i;?>"><?= $i;?></a></li>
				<?php
						}
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