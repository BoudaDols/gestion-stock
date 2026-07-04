<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();

	$pagetitle = "GSF | M.A.J des infos.";
	$pagestitle = "Mise à jour des informations"; // A remplacer après
	$bcrumb = "Paramétrage > M.A.J Entête";
	$display = "style='display:none'"; //Sert à afficher/cacher le btn 'annuler la modif'
	
	$msg = "";
	$classmsg = "";
	$action = "";
	$button = "";
	
	$sql = "SELECT * FROM entete WHERE id=1";
	$infos = SQLSelect($sql);
	foreach($infos as $info):
		$anclogo = $info->logo;
		$nom = stripslashes($info->nom);
		$adresse = stripslashes($info->adresse);
		$tel1 = stripslashes($info->tel1);
		$tel2 = stripslashes($info->tel2);
		$banque = stripslashes($info->banque);
		$rccm = stripslashes($info->rccm);
		$ifu = stripslashes($info->ifu);
	endforeach;
	// echo "<a href='".$anclogo."'>logo</a>"; exit;
	if(isset($_POST['btnsubmit']))
	{
		$tmp = $_FILES['logo']['tmp_name'];
		$name = $_FILES['logo']['name'];
		$nom = addslashes($_POST['nom']);
		$adresse = addslashes($_POST['adresse']);
		$tel1 = addslashes($_POST['tel1']);
		$tel2 = addslashes($_POST['tel2']);
		$banque = addslashes($_POST['banque']);
		$rccm = addslashes($_POST['rccm']);
		$ifu = addslashes($_POST['ifu']);
		
		$exts = ['jpg','jpeg','png','gif'];
		$ext = substr($name,strripos($name,'.')+1);
		
		if(!in_array($ext,$exts))
		{
			$msg = "Format logo incorrect!<br>Sont autorisés les format (JPEG, JPG, PGN, GIF).";
			$classmsg = "alert alert-warning";
			$button = "<button type='button' class='close' data-dismiss='alert' 
			aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";	
		}
		else
		{
			$lelogo = "lelogo.".$ext;
			$dest = "dist/img/".$lelogo;
		
			if(file_exists($dest))
			{
				if(unlink($dest))//supprimer l'ancien logo
				{
					if(move_uploaded_file($tmp, $dest))
					{
						$req = $bdd->db->PREPARE("UPDATE entete SET logo=:logo,nom=:nom,adresse=:adr,tel1=:tel1,
						tel2=:tel2,banque=:bank,rccm=:rccm,ifu=:ifu WHERE id=:id");
						$req->EXECUTE(ARRAY('logo'=>$dest,'nom'=>$nom,'adr'=>$adresse,'tel1'=>$tel1,
						'tel2'=>$tel2,'bank'=>$banque,'rccm'=>$rccm,'ifu'=>$ifu,'id'=>1));
						
						$msg = "Informations mises à jour!";
						$classmsg = "alert alert-success";
						$button = "<button type='button' class='close' data-dismiss='alert' 
						aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";
					}
					else
					{
						$msg = "Erreur lors de la copie du logo";
						$classmsg = "alert alert-danger";
						$button = "<button type='button' class='close' data-dismiss='alert' 
						aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";	
					}
				}
				else
				{
					$msg = "Erreur lors de la suppression de l'ancienne version du logo";
					$classmsg = "alert alert-danger";
					$button = "<button type='button' class='close' data-dismiss='alert' 
					aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";
				}
			}
			else
			{
				if(move_uploaded_file($tmp, $dest))
				{
					$req = $bdd->db->PREPARE("UPDATE entete SET logo=:logo,nom=:nom,adresse=:adr,tel1=:tel1,
					tel2=:tel2,banque=:bank,rccm=:rccm,ifu=:ifu WHERE id=:id");
					$req->EXECUTE(ARRAY('logo'=>$dest,'nom'=>$nom,'adresse'=>$adresse,'tel1'=>$tel1,
					'tel2'=>$tel2,'bank'=>$banque,'rccm'=>$rccm,'ifu'=>$ifu));
					
					$msg = "Informations mises à jour!";
					$classmsg = "alert alert-success";
					$button = "<button type='button' class='close' data-dismiss='alert' 
					aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";
				}
				else
				{
					$msg = "Erreur lors de la copie du logo";
					$classmsg = "alert alert-danger";
					$button = "<button type='button' class='close' data-dismiss='alert' 
					aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";	
				}
			}
		}
	}
	
	ob_start();
?>

	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-adjust"></i>
				<h3 class="box-title">Modification Infos. Entête</h3>
			</div>
			<form name="majinfos" method="POST" enctype="multipart/form-data">
				<div class="box-body">
					<div class="row col-lg-4">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-barcode"></i>
								</div>
								<input type="file" class="form-control" style="width:200px" name="logo" required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-8">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-font"></i>
								</div>
								<input type="text" class="form-control" style="width:500px" name="nom" placeholder="Raison sociale" value="<?= $nom;?>" required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-3">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-font"></i>
								</div>
								<input type="text" class="form-control" style="width:150px" name="tel1" placeholder="Tel.1" value="<?= $tel1;?>" required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-3">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-font"></i>
								</div>
								<input type="text" class="form-control" style="width:150px" name="tel2" placeholder="Tel.2" value="<?= $tel2;?>"/>
							</div>
						</div>
					</div>
					<div class="row col-lg-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-font"></i>
								</div>
								<input type="text" class="form-control" style="width:370px" name="adresse" placeholder="Adresse" value="<?= $adresse;?>" required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-12">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-font"></i>
								</div>
								<input type="text" class="form-control" style="width:480px" name="banque" placeholder="Banque" value="<?= $banque;?>" required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-4">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-font"></i>
								</div>
								<input type="text" class="form-control" style="width:200px" name="rccm" placeholder="RCCM" value="<?= $rccm;?>" required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-4">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-font"></i>
								</div>
								<input type="text" class="form-control" style="width:200px" name="ifu" placeholder="IFU" value="<?= $ifu;?>" required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-4">
						<input type="submit" name="btnsubmit" class="btn btn-primary" value="VALIDER">
					</div>
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
	$content = ob_get_clean();
	require_once('patterning.php');
?>