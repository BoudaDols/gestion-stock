<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();
	
	$pagetitle = "GSF | M.A.J des catégories de factures";
	$pagestitle = "Mise à jour des catégories de factures"; // A remplacer après
	$bcrumb = "Paramétrage > M.A.J Catgéories Factures";
	$display = "style='display:none'"; //Sert à afficher/cacher le btn 'annuler la modif'
	
	$btnaction = "insert";
	$disabledc = ""; //Sert à griser le code pour update
	$disabled = ""; //Sert à griser les champs après insert ou update
	$tableau = "entier"; //Différencier l'?age du tableau: si tout le tableau ou une recherche
	$msg = "";
	$classmsg = "";
	$button = "";
	$action = "";
	
	$codeTF = "";
	$nomTF = "";
	
	//Pagination
	$parpage = 10;
	$sql = "SELECT * FROM typefacture";
	$nblignes = count(SQLSelect($sql));
	$nbpages = ceil($nblignes/$parpage);
	
	if(isset($_GET['action']))
	{
		$getaction = $_GET['action'];
		$getcode = $_GET['code'];
		
		if($getaction=="edit")
		{
			$sqledit = "SELECT * FROM typefacture WHERE codeTypeF='$getcode'";
			$edits = SQLSelect($sqledit);
			foreach($edits as $edit):
				$codeTF = $getcode;
				$nomTF = stripslashes($edit->designationTypeF);
			endforeach;
			$btnaction = "update";
			$disabledc = "disabled";
			$display = "style='display:inline'";
		}
		elseif($getaction=="statut")
		{
			$sqlrechstat = "SELECT * FROM typefacture WHERE codeTypeF='$getcode'";
			$lestats = SQLSelect($sqlrechstat);
			foreach($lestats as $lestat):
				$oldstat = $lestat->statutTypeF;
			endforeach;
			if($oldstat=="ON")
			{
				$sqlstat = $bdd->db->PREPARE("UPDATE typefacture SET statutTypeF=:nstat WHERE codeTypeF=:getcode");
				$sqlstat->EXECUTE(array('nstat'=>'OFF', 'getcode'=>$getcode));
				
				if($sqlstat)
				{
					$msg="Catégorie desactivée!";
					$classmsg = "alert alert-warning";
					$action = "<br><br><br><a href='majtypefacture.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabledc = "disabled";
					$disabled = "disabled";
				}
				
			}
			else
			{
				$sqlstat = $bdd->db->PREPARE("UPDATE typefacture SET statutTypeF=:nstat WHERE codeTypeF=:getcode");
				$sqlstat->EXECUTE(array('nstat'=>'ON', 'getcode'=>$getcode));
				
				if($sqlstat)
				{
					$msg="Catégorie activée!";
					$classmsg = "alert alert-success";
					$action = "<br><br><br><a href='majtypefacture.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
				
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
			
			$codeTF = $_POST['codeTF'];
			$nomTF = addslashes($_POST['nomTF']);
			$stat = "ON";
			
			//Vérifier si le même code n'est pas déjà utilisé
			$verifCode = "SELECT * FROM typefacture WHERE codeTypeF='$codeTF'";
			$result = SQLSelect($verifCode);
			if(!empty($result))
			{
				$msg = "Code déjà attribué à une catégorie!<br>Créez-en un autre.";
				$classmsg = "alert alert-info";
				$button = "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";				
			}
			else
			{
				$sql = $bdd->db->PREPARE("INSERT INTO typefacture 
										(codeTypeF, designationTypeF, statutTypeF) 
										VALUES(:code, :nom, :stat)
										");
				$sql->EXECUTE(array('code' => $codeTF, 'nom' => $nomTF, 'stat' => $stat));
				
				if($sql)
				{
					$msg="Catégorie créée avec succès!";
					$classmsg = "alert alert-success";
					$action = "<br><br><br><a href='majtypefacture.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabledc = "disabled";
					$disabled = "disabled";
				}
				else
				{
					$msg="Erreur lors de la création de la catégorie";
					$classmsg = "alert alert-warning";
					$action = "<br><br><br><a href='majtypefacture.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabledc = "disabled";
					$disabled = "disabled";
				}
			}
		}
		else
		{
			$nomTF = addslashes($_POST['nomTF']);
			
			$sql = $bdd->db->PREPARE("UPDATE typefacture SET designationTypeF=:nom WHERE codeTypeF=:getcode");
			$sql->EXECUTE(array('nom'=>$nomTF,'getcode'=>$getcode));
			
			if($sql)
			{
				$msg="Catégorie modifiée avec succès!";
				$classmsg = "alert alert-success";
				$action = "<br><br><br><a href='majtypefacture.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";

				$disabledc = "disabled";
				$disabled = "disabled";
				
				$display = "style='display:none'";
			}
			else
			{
				$msg="Erreur lors de la modification de la catégorie";
				$classmsg = "alert alert-warning";
				$action = "<br><br><br><a href='majtypefacture.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
				
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
			$sqlrech = "SELECT * FROM typefacture LIMIT $first, $parpage";
			$tableau = "entier";
		}
		else
		{
			$sqlrech = "SELECT * FROM typefacture WHERE codeTypeF LIKE '%$rech%' OR designationTypeF LIKE '%$rech%' LIMIT $first, $parpage";
			$tableau = "rechercher";
		}
		
	}
	
	ob_start();
?>
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-tag"></i>
				<h3 class="box-title">Ajout/Modification Catgéorie</h3>
			</div>
			<form name="majtypefacture" method="POST">
				<div class="box-body">
					<div class="row col-lg-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-barcode"></i>
								</div>
								<input type="text" class="form-control" style="width:350px" name="codeTF" placeholder="Code" value="<?= $codeTF;?>" <?= $disabledc; ?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-font"></i>
								</div>
								<input type="text" class="form-control" style="width:350px" name="nomTF" placeholder="Désignation" value="<?= $nomTF;?>" <?= $disabled; ?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-3"></div>
					<div class="row col-lg-3">
						<input type="hidden" name="btnaction" value="<?= $btnaction; ?>">
						<input type="submit" name="btnsubmit" class="btn btn-primary" value="VALIDER" <?= $disabled; ?>>
					</div>
					<div class="row col-lg-3" <?= $display;?> >
						<a href="majtypefacture.php">
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
		$sqlentier = "SELECT * FROM typefacture LIMIT $first, $parpage";
		if($tableau=="entier")
		{
			$categ = SQLSelect($sqlentier);
		}
		else
		{
			$categ = SQLSelect($sqlrech);
		}
	?>
	
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-filter"></i>
				<h3 class="box-title">Liste des catégories de factures</h3>
			</div>
			<div class="row">
					<div class="col-lg-4"></div>
					<div class="col-lg-4"></div>
					<div class="col-lg-4">
						<form role="form" class="form-inline" name="rechcateg" action="" method="post">
							<input type="text" name="research" placeholder="Code/Désignation" class="form-control">
							<button class="btn btn-info btn-flat" name="btnresearch" type="submit">Lister</button>
						</form>
					</div>
				</div>
			<div class="box-body">
				
				<table class="table table-bordered" name="tabtypef" id="tabtypef">
					<thead>
						<tr>
							<th style="width:50px">#</th>
							<th style="width:100px">CODE</th>
							<th>LIBELLE</th>
							<th style="width:50px"></th>
							<th style="width:50px"></th>
						</tr>
					</thead>
					<tbody>
						<?php
							if(empty($categ))
							{
						?>
								<tr>
									<td colspan="5">Aucune catégorie de factures dans la base.</td>
								</tr>
						<?php
							}
							else
							{
								foreach($categ as $cat):
						?>
									<tr>
										<td><?= $numligne++;?></td>
										<td><?= $cat->codeTypeF; ?></td>
										<td><?= stripslashes($cat->designationTypeF) ?></td>
										<td>
											<a href="majtypefacture.php?action=edit&code=<?= $cat->codeTypeF; ?>">
												<button class='btn bg-orange'>EDITER</button>
											</a>
										</td>
										<td>
											<a href="majtypefacture.php?action=statut&code=<?= $cat->codeTypeF; ?>">
												<?php
													if($cat->statutTypeF=="ON")
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
							<th style="width:50px">#</th>
							<th style="width:100px">CODE</th>
							<th>LIBELLE</th>
							<th style="width:50px"></th>
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
								<li><a href="majtypefacture.php?page=<?= $i;?>"><?= $i;?></a></li>
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