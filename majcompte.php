<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();
	
	$pagetitle = "GSF | M.A.J des comptes";
	$pagestitle = "Mise à jour des comptes"; // A remplacer après
	$bcrumb = "Paramétrage > M.A.J Comptes";
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
	
	//Pagination
	$parpage = 10;
	$sql = "SELECT * FROM compte";
	$nblignes = count(SQLSelect($sql));
	$nbpages = ceil($nblignes/$parpage);
	
	if(isset($_GET['action']))
	{
		$getaction = $_GET['action'];
		$getcode = $_GET['code'];
		
		if($getaction=="edit")
		{
			$sqledit = "SELECT * FROM compte WHERE codeCompte='$getcode'";
			$edits = SQLSelect($sqledit);
			foreach($edits as $edit):
				$codeC = $getcode;
				$nomC = $edit->libelleCompte;
			endforeach;
			$btnaction = "update";
			$disabledc = "disabled";
			$display = "style='display:inline'";
		}
	}
	
	if(isset($_POST['btnsubmit']))
	{
		$btnaction = $_POST['btnaction'];
		
		if($btnaction=="insert")
		{
			$codeC = $_POST['codeC'];
			$nomC = addslashes($_POST['nomC']);
			//Vérifier si le même code n'est pas déjà utilisé
			$verifCode = "SELECT * FROM compte WHERE codeCompte='$codeC'";
			$result = SQLSelect($verifCode);
			if(!empty($result))
			{
				$msg = "Code déjà attribué à un compte!<br>Créez-en un autre.";
				$classmsg = "alert alert-info";
				$button = "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";				
			}
			else
			{
				$sql = $bdd->db->PREPARE("INSERT INTO compte (codeCompte,libelleCompte) 
				VALUES(:code,:nom)");
				$sql->EXECUTE(array('code'=>$codeC,'nom'=>$nomC));
				if($sql)
				{
					$msg="Compte créé avec succès!";
					$classmsg = "alert alert-success";
					$action = "<br><br><br><a href='majcompte.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabledc = "disabled";
					$disabled = "disabled";
				}
				else
				{
					$msg="Erreur lors de la création du compte";
					$classmsg = "alert alert-warning";
					$action = "<br><br><br><a href='majcompte.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabledc = "disabled";
					$disabled = "disabled";
				}
			}
		}
		else
		{
			$nomC = addslashes($_POST['nomC']);
			
			$sql = $bdd->db->PREPARE("UPDATE compte SET libelleCompte=:nom WHERE codeCompte=:getcode");
			$sql->EXECUTE(array('nom'=>$nomC,'getcode'=>$getcode));
			
			if($sql)
			{
				$msg="Compte modifié avec succès!";
				$classmsg = "alert alert-success";
				$action = "<br><br><br><a href='majcompte.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";

				$disabledc = "disabled";
				$disabled = "disabled";
				
				$display = "style='display:none'";
			}
			else
			{
				$msg="Erreur lors de la modification du compte";
				$classmsg = "alert alert-warning";
				$action = "<br><br><br><a href='majcompte.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
				
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
			$sqlrech = "SELECT * FROM compte LIMIT $first, $parpage";
			$tableau = "entier";
		}
		else
		{
			$sqlrech = "SELECT * FROM compte WHERE codeCompte LIKE '%$rech%' OR libelleCompte LIKE '%$rech%' LIMIT $first, $parpage";
			$tableau = "rechercher";
		}
		
	}
	
	ob_start();
?>


	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-tag"></i>
				<h3 class="box-title">Ajout/Modification Comptes</h3>
			</div>
			<form name="majcompte" method="POST">
				<div class="box-body">
					<div class="row col-lg-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i>Code</i>
								</div>
								<input type="text" class="form-control" style="width:350px" name="codeC" placeholder="Code" value="<?= $codeC;?>" <?= $disabledc; ?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i>Libellé</i>
								</div>
								<input type="text" class="form-control" style="width:350px" name="nomC" placeholder="Libellé" value="<?= stripslashes($nomC);?>" <?= $disabled; ?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-3"></div>
					<div class="row col-lg-3">
						<input type="hidden" name="btnaction" value="<?= $btnaction; ?>">
						<input type="submit" name="btnsubmit" class="btn btn-primary" value="VALIDER" <?= $disabled; ?>>
					</div>
					<div class="row col-lg-3" <?= $display;?> >
						<a href="majcompte.php">
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
		$sqlentier = "SELECT * FROM compte LIMIT $first, $parpage";
		if($tableau=="entier")
		{
			$cpts = SQLSelect($sqlentier);
		}
		else
		{
			$cpts = SQLSelect($sqlrech);
		}
	?>
	
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-filter"></i>
				<h3 class="box-title">Liste des comptes</h3>
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
				
				<table class="table table-bordered" name="tabcompte" id="tabcompte">
					<thead>
						<tr>
							<th style="width:50px">#</th>
							<th style="width:100px">CODE</th>
							<th>LIBELLE</th>
							<th style="width:50px"></th>
						</tr>
					</thead>
					<tbody>
						<?php
							if(empty($cpts))
							{
						?>
								<tr>
									<td colspan="4">Aucun compte dans la base.</td>
								</tr>
						<?php
							}
							else
							{
								foreach($cpts as $cpt):
						?>
									<tr>
										<td><?= $numligne++;?></td>
										<td><?= $cpt->codeCompte; ?></td>
										<td><?= stripslashes($cpt->libelleCompte) ?></td>
										<td>
											<a href="majcompte.php?action=edit&code=<?= $cpt->codeCompte; ?>">
												<button class='btn bg-orange'>EDITER</button>
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
							<th style="width:100px">CODE</th>
							<th>LIBELLE</th>
							<th style="width:50px"></th>
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
								<li><a href="majcompte.php?page=<?= $i;?>"><?= $i;?></a></li>
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