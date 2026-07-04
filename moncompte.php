<?php
	session_start();
	require_once('php/fonction.php');
	$bdd = new DB();
	
	$pagetitle = "GSF | Mon profil";
	$pagestitle = "Informations personnelles"; // A remplacer après
	$bcrumb = "Paramétrage > Mon profil";
	
	$msg = "";
	$classmsg = "";
	$button = "";

	if(isset($_POST['btnsubmit']))
	{
		$passe = md5($_POST['mdpasse']);
		$sql = $bdd->db->PREPARE("UPDATE user SET mdpUser=:mdp 
		WHERE loginUser=:login");
		$sql->EXECUTE(array('mdp'=>$passe,'login'=>$_SESSION['user']));
		if($sql)
		{
			$msg = "Mot de passe modifié!";
			$classmsg = "alert alert-success";
			$button = "<button type='button' class='close' data-dismiss='alert' 
			aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";
		}
	}
	
	ob_start();
?>
	
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-user"></i>
				<h3 class="box-title">Infos. personnelles</h3>
			</div>
			<form name="majusers" method="POST">
				<div class="box-body">
					<div class="row col-lg-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i>Nom & Prénoms</i>
								</div>
								<input type="text" disabled="disabled" value="<?=$_SESSION['nom'];?>" />
							</div>
						</div>
					</div>
					<div class="row col-lg-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i>Login</i>
								</div>
								<input type="text" disabled="disabled" value="<?=$_SESSION['user'];?>" />
							</div>
						</div>
					</div>
					<div class="row col-lg-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i>Compte</i>
								</div>
								<input type="text" disabled="disabled" value="<?=$_SESSION['compte'];?>" />
							</div>
						</div>
					</div>
					<div class="row col-lg-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i>Passe</i>
								</div>
								<input type="password" class="form-control" style="width:350px" 
								name="mdpasse" required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-4"></div>
					<div class="row col-lg-4">
						<input type="submit" name="btnsubmit" class="btn btn-primary" value="VALIDER">
					</div>
					<div class="row col-lg-4"></div>
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
			</div>
		</div>
		<div class="col-lg-3"></div>
	</div>
	
<?php
	$content = ob_get_clean();
	require_once('patterning.php');
?>	