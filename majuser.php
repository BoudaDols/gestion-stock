<?php
	require_once('php/session.php');
	require_once('php/fonction.php');
	
	$pagetitle = "GSF | M.A.J des utilisateurs";
	$pagestitle = "Mise à jour des utilisateurs"; // A remplacer après
	$bcrumb = "Paramétrage > M.A.J Utilisateurs";
	$display = "style='display:none'"; //Sert à afficher/cacher le btn 'annuler la modif'
	
	$btnaction = "insert";
	$disabled = ""; //Sert à griser les champs après insert ou update
	$tableau = "entier"; //Différencier l'?age du tableau: si tout le tableau ou une recherche
	$msg = "";
	$classmsg = "";
	$button = "";
	$action = "";
	
	$nomU = "";
	$prenomU = "";
	$loginU = "";
	$compteU = "";
	
	//Pagination
	$parpage = 10;
	$result = SQLSelect("SELECT * FROM user");
	$nblignes = $result ? count($result) : 0;
	$nbpages = ceil($nblignes/$parpage);
	
	if(isset($_GET['action']))
	{
		$getaction = $_GET['action'];
		$getcode = $_GET['code'];
		
		if($getaction=="edit")
		{
			$edits = SQLSelect("SELECT * FROM user WHERE idUser = :code", [':code' => $getcode]);
			foreach($edits as $edit):
				$idUser = $edit->idUser;
				$nomU = $edit->nomUser;
				$prenomU = $edit->prenomUser;
				$loginU = $edit->loginUser;
			endforeach;
			$btnaction = "update";
			$display = "style='display:inline'";
		}
		if($getaction=="stat")
		{
			$edits = SQLSelect("SELECT * FROM user WHERE idUser = :code", [':code' => $getcode]);
			foreach($edits as $edit):
				$idUser = $edit->idUser;
				$statut = $edit->statutCompteUser;
			endforeach;
			if($statut==1)
			{
				SQLExecute("UPDATE user SET statutCompteUser=:nstat 
				WHERE idUser=:getcode", ['nstat'=>0,'getcode'=>$getcode]);
				$msg="Utilisateur desactivé avec succès!";
				$classmsg = "alert alert-success";
				$action = "<br><br><br><a href='majuser.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
				
				$disabled = "disabled";
			}
			else
			{
				SQLExecute("UPDATE user SET statutCompteUser=:nstat 
				WHERE idUser=:getcode", ['nstat'=>1,'getcode'=>$getcode]);
				$msg="Utilisateur activé avec succès!";
				$classmsg = "alert alert-success";
				$action = "<br><br><br><a href='majuser.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
				
				$disabled = "disabled";
			}
		}
		if($getaction=="reinit")
		{
			$edits = SQLSelect("SELECT * FROM user WHERE idUser = :code", [':code' => $getcode]);
			foreach($edits as $edit):
				$loginU = $edit->loginUser;
			endforeach;
			SQLExecute("UPDATE user SET mdpUser=:mdp 
			WHERE idUser=:getcode", ['mdp'=>password_hash($loginU, PASSWORD_DEFAULT),'getcode'=>$getcode]);
			$msg="Mot de passe reinitialisé avec succès!";
			$classmsg = "alert alert-success";
			$action = "<br><br><br><a href='majuser.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
			
			$disabled = "disabled";
		}
	}
	
	if(isset($_POST['btnsubmit']))
	{
		$btnaction = $_POST['btnaction'];
		
		if($btnaction=="insert")
		{
			$nomU = $_POST['nomU'];
			$prenomU = $_POST['prenomU'];
			$loginU = $_POST['loginU'];
			$compteU = $_POST['compteU'];
			//Vérifier si le même login n'est pas déjà utilisé
			$result = SQLSelect("SELECT * FROM user WHERE loginUser = :login", [':login' => $loginU]);
			if(!empty($result))
			{
				$msg = "Login déjà attribué à un utilisateur!<br>Créez-en un autre.";
				$classmsg = "alert alert-info";
				$button = "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";				
			}
			else
			{
				$success = SQLExecute("INSERT INTO user (nomUser,prenomUser,loginUser,mdpUser,
				statutCompteUser,user_codeCompte) 
				VALUES(:nom,:prenom,:login,:mdp,:statut,:compte)",
				['nom'=>$nomU,'prenom'=>$prenomU,'login'=>$loginU,
				'mdp'=>password_hash($loginU, PASSWORD_DEFAULT),'statut'=>1,'compte'=>$compteU]);
				if($success)
				{
					$msg="Utilisateur créé avec succès!";
					$classmsg = "alert alert-success";
					$action = "<br><br><br><a href='majuser.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabledc = "disabled";
					$disabled = "disabled";
				}
				else
				{
					$msg="Erreur lors de la création de l'utilisateur";
					$classmsg = "alert alert-warning";
					$action = "<br><br><br><a href='majuser.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabledc = "disabled";
					$disabled = "disabled";
				}
			}
		}
		else
		{
			$nomU = $_POST['nomU'];
			$prenomU = $_POST['prenomU'];
			$loginU = $_POST['loginU'];
			$compteU = $_POST['compteU'];
			//Vérifier si le même login n'est pas déjà utilisé
			$result = SQLSelect("SELECT * FROM user WHERE loginUser = :login AND idUser != :code", [':login' => $loginU, ':code' => $getcode]);
			if(!empty($result))
			{
				$msg = "Login déjà attribué à un utilisateur!<br>Créez-en un autre.";
				$classmsg = "alert alert-info";
				$button = "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";				
			}
			else
			{
				$success = SQLExecute("UPDATE user SET nomUser=:nom,prenomUser=:prenom, 
				loginUser=:login,user_codeCompte=:compte WHERE idUser=:getcode",
				['nom'=>$nomU,'prenom'=>$prenomU,'login'=>$loginU,
				'compte'=>$compteU,'getcode'=>$getcode]);
				if($success)
				{
					$msg="Utilisateur modifié avec succès!";
					$classmsg = "alert alert-success";
					$action = "<br><br><br><a href='majuser.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabledc = "disabled";
					$disabled = "disabled";
				}
				else
				{
					$msg="Erreur lors de la modification de l'utilisateur";
					$classmsg = "alert alert-warning";
					$action = "<br><br><br><a href='majuser.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabledc = "disabled";
					$disabled = "disabled";
				}
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
			$tableau = "entier";
		}
		else
		{
			$tableau = "rechercher";
		}
	}
	
	ob_start();
?>
	
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-user"></i>
				<h3 class="box-title">Ajout/Modification Utilisateurs</h3>
			</div>
			<form name="majusers" method="POST">
				<div class="box-body">
					<div class="row col-lg-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i>Nom</i>
								</div>
								<input type="text" class="form-control" style="width:350px" name="nomU" placeholder="Nom" value="<?= $nomU;?>" <?= $disabled; ?> required/>
							</div>
						</div>
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i>Prénoms</i>
								</div>
								<input type="text" class="form-control" style="width:350px" name="prenomU" placeholder="Prénom" value="<?= $prenomU;?>" <?= $disabled; ?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i>Login</i>
								</div>
								<input type="text" class="form-control" style="width:350px" name="loginU" placeholder="Login" value="<?= $loginU;?>" <?= $disabled; ?> required/>
							</div>
						</div>
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i>Compte</i>
								</div>
								<?php
									$cpts=SQLSelect("SELECT * FROM compte");
								?>
								<select class="form-control" type="text" style="width:400px" 
								name="compteU" <?=$disabled;?> >
									<?php foreach ($cpts as $cpt):?>
										<option value="<?=$cpt->codeCompte;?>">
											<?=$cpt->libelleCompte;?>
										</option>
									<?php endforeach;?>
								</select>
							</div>
						</div>
					</div>
					<div class="row col-lg-3"></div>
					<div class="row col-lg-3">
						<input type="hidden" name="btnaction" value="<?= $btnaction; ?>">
						<input type="submit" name="btnsubmit" class="btn btn-primary" value="VALIDER" <?= $disabled; ?>>
					</div>
					<div class="row col-lg-3" <?= $display;?> >
						<a href="majuser.php">
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
		if($tableau=="entier")
		{
			$users = SQLSelect("SELECT * FROM user LIMIT :offset, :limit", [':offset' => $first, ':limit' => $parpage]);
		}
		else
		{
			$users = SQLSelect("SELECT * FROM user WHERE loginUser LIKE :rech OR nomUser LIKE :rech OR prenomUser LIKE :rech LIMIT :offset, :limit", [':rech' => "%{$rech}%", ':offset' => $first, ':limit' => $parpage]);
		}
	?>
	
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-filter"></i>
				<h3 class="box-title">Liste des utilisateurs</h3>
			</div>
			<div class="row">
				<div class="col-lg-4"></div>
				<div class="col-lg-4"></div>
				<div class="col-lg-4">
					<form role="form" class="form-inline" name="rechcpt" action="" method="post">
						<input type="text" name="research" placeholder="Code/Libellé" class="form-control">
						<button class="btn btn-info btn-flat" name="btnresearch" type="submit">Lister</button>
					</form>
				</div>
			</div>
			<div class="box-body">
				<table class="table table-bordered" name="tabuser" id="tabuser">
					<thead>
						<tr>
							<th style="width:50px">#</th>
							<th>NOM</th>
							<th>PRENOM</th>
							<th>LOGIN</th>
							<th>COMPTE</th>
							<th colspan="3"></th>
						</tr>
					</thead>
					<tbody>
						<?php
							if(empty($users))
							{
						?>
								<tr>
									<td colspan="8">Aucun utilisateur dans la base.</td>
								</tr>
						<?php
							}
							else
							{
								foreach($users as $user):
						?>
									<tr>
										<td><?= $numligne++;?></td>
										<td><?= $user->nomUser; ?></td>
										<td><?= $user->prenomUser ?></td>
										<td><?= $user->loginUser; ?></td>
										<td><?= $user->user_codeCompte; ?></td>
										<td style="width:50px">
											<a href="majuser.php?action=edit&code=<?=$user->idUser; ?>">
												<button class='btn bg-blue'>EDITER</button>
											</a>
										</td>
										<td style="width:50px">
											<a href="majuser.php?action=stat&code=<?=$user->idUser; ?>">
												<button class='btn bg-yellow'>
													<?php
														if($user->statutCompteUser==1)
															echo "DESAC.";
														else
															echo "ACT.";
													?>
												</button>
											</a>
										</td>
										<td style="width:50px">
											<a href="majuser.php?action=reinit&code=<?=$user->idUser; ?>">
												<button class='btn bg-red'>REINIT.</button>
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
							<th>NOM</th>
							<th>PRENOM</th>
							<th>LOGIN</th>
							<th>COMPTE</th>
							<th colspan="3"></th>
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
								<li><a href="majuser.php?page=<?= $i;?>"><?= $i;?></a></li>
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
