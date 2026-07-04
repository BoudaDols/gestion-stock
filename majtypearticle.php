<?php
	require_once('php/session.php');
	require_once('php/fonction.php');
	
	$pagetitle = "GSF | M.A.J des catégories d'artciles";
	$pagestitle = "Mise à jour des catégories d'articles"; // A remplacer après
	$bcrumb = "Paramétrage > M.A.J Catgéories Articles";
	$display = "style='display:none'"; //Sert à afficher/cacher le btn 'annuler la modif'
	
	$btnaction = "insert";
	$disabledc = ""; //Sert à griser le code pour update
	$disabled = ""; //Sert à griser les champs après insert ou update
	$tableau = "entier"; //Différencier l'?age du tableau: si tout le tableau ou une recherche
	$msg = "";
	$classmsg = "";
	$button = "";
	$action = "";
	
	$codeTA = "";
	$nomTA = "";
	
	//Pagination
	$parpage = 10;
	$result = SQLSelect("SELECT * FROM typearticle");
	$nblignes = $result ? count($result) : 0;
	$nbpages = ceil($nblignes/$parpage);
	
	if(isset($_GET['action']))
	{
		$getaction = $_GET['action'];
		$getcode = $_GET['code'];
		
		if($getaction=="edit")
		{
			$edits = SQLSelect("SELECT * FROM typearticle WHERE codeTypeA = :code", [':code' => $getcode]);
			foreach($edits as $edit):
				$codeTA = $getcode;
				$nomTA = $edit->designationTypeA;
			endforeach;
			$btnaction = "update";
			$disabledc = "disabled";
			$display = "style='display:inline'";
		}
		elseif($getaction=="statut")
		{
			$lestats = SQLSelect("SELECT * FROM typearticle WHERE codeTypeA = :code", [':code' => $getcode]);
			foreach($lestats as $lestat):
				$oldstat = $lestat->statutTypeA;
			endforeach;
			if($oldstat=="ON")
			{
				//d'abord desactiver les articles en lien
				$art = SQLSelect("SELECT * FROM article WHERE article_codeTypeA = :code", [':code' => $getcode]);

				SQLExecute("UPDATE typearticle SET statutTypeA=:nstat WHERE codeTypeA=:getcode",
				['nstat'=>'OFF', 'getcode'=>$getcode]);
				
				$msg="Catégorie desactivée!";
				$classmsg = "alert alert-warning";
				$action = "<br><br><br><a href='majtypearticle.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
			
				$disabledc = "disabled";
				$disabled = "disabled";
			}
			else
			{
				//d'abord activer les articles en lien
				$art = SQLSelect("SELECT * FROM article WHERE article_codeTypeA = :code", [':code' => $getcode]);

				SQLExecute("UPDATE typearticle SET statutTypeA=:nstat WHERE codeTypeA=:getcode",
				['nstat'=>'ON', 'getcode'=>$getcode]);
				
				$msg="Catégorie activée!";
				$classmsg = "alert alert-success";
				$action = "<br><br><br><a href='majtypearticle.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
			
				$disabledc = "disabled";
				$disabled = "disabled";
			}
		}
			
	}
	
	if(isset($_POST['btnsubmit']))
	{
		$btnaction = $_POST['btnaction'];
		
		if($btnaction=="insert")
		{
			$codeTA = $_POST['codeTA'];
			$nomTA = $_POST['nomTA'];
			$stat = "ON";
			
			//Vérifier si le même code n'est pas déjà utilisé
			$result = SQLSelect("SELECT * FROM typearticle WHERE codeTypeA = :code", [':code' => $codeTA]);
			if(!empty($result))
			{
				$msg = "Code déjà attribué à une catégorie!<br>Créez-en un autre.";
				$classmsg = "alert alert-info";
				$button = "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";				
			}
			else
			{
				$success = SQLExecute("INSERT INTO typearticle 
										(codeTypeA, designationTypeA, statutTypeA) 
										VALUES(:code, :nom, :stat)",
										['code' => $codeTA, 'nom' => $nomTA, 'stat' => $stat]);
				
				if($success)
				{
					$msg="Catégorie créée avec succès!";
					$classmsg = "alert alert-success";
					$action = "<br><br><br><a href='majtypearticle.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabledc = "disabled";
					$disabled = "disabled";
				}
				else
				{
					$msg="Erreur lors de la création de la catégorie";
					$classmsg = "alert alert-warning";
					$action = "<br><br><br><a href='majtypearticle.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
				
					$disabledc = "disabled";
					$disabled = "disabled";
				}
			}
			
		}
		else
		{
			$nomTA = $_POST['nomTA'];
			
			$success = SQLExecute("UPDATE typearticle SET designationTypeA=:nom WHERE codeTypeA=:getcode",
			['nom'=>$nomTA,'getcode'=>$getcode]);
			
			if($success)
			{
				$msg="Catégorie modifiée avec succès!";
				$classmsg = "alert alert-success";
				$action = "<br><br><br><a href='majtypearticle.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
				
				$disabledc = "disabled";
				$disabled = "disabled";
					
				$display = "style='display:none'";
			}
			else
			{
				$msg="Erreur lors de la modification de la catégorie";
				$classmsg = "alert alert-warning";
				$action = "<br><br><br><a href='majtypearticle.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
			
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
				<i class="fa fa-asterisk"></i>
				<h3 class="box-title">Ajout/Modification Catgéorie</h3>
			</div>
			<form name="majclient" method="POST">
				<div class="box-body">
					<div class="row col-lg-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-barcode"></i>
								</div>
								<input type="text" class="form-control" style="width:350px" name="codeTA" placeholder="Code de l'article" value="<?= $codeTA;?>" <?= $disabledc; ?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-font"></i>
								</div>
								<input type="text" class="form-control" style="width:350px" name="nomTA" placeholder="Libellé de l'article" value="<?= $nomTA;?>" <?= $disabled; ?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-3"></div>
					<div class="row col-lg-3">
						<input type="hidden" name="btnaction" value="<?= $btnaction; ?>">
						<input type="submit" name="btnsubmit" class="btn btn-primary" value="VALIDER" <?= $disabled; ?>>
					</div>
					<div class="row col-lg-3" <?= $display;?> >
						<a href="majtypearticle.php">
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
			$categ = SQLSelect("SELECT * FROM typearticle LIMIT :offset, :limit", [':offset' => $first, ':limit' => $parpage]);
		}
		else
		{
			$categ = SQLSelect("SELECT * FROM typearticle WHERE codeTypeA LIKE :rech OR designationTypeA LIKE :rech LIMIT :offset, :limit", [':rech' => "%{$rech}%", ':offset' => $first, ':limit' => $parpage]);
		}
	?>
	
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-filter"></i>
				<h3 class="box-title">Liste des catégories d'articles</h3>
			</div>
			<div class="row">
					<div class="col-lg-4"></div>
					<div class="col-lg-4"></div>
					<div class="col-lg-4">
						<form role="form" class="form-inline" name="rechclient" action="" method="post">
							<input type="text" name="research" placeholder="Code/Désignation" class="form-control">
							<button class="btn btn-info btn-flat" name="btnresearch" type="submit">Lister</button>
						</form>
					</div>
				</div>
			<div class="box-body">
				
				<table class="table table-bordered" name="tabtypea" id="tabtypea">
					<thead>
						<tr>
							<th  style="width:50px">#</th>
							<th  style="width:100px">CODE</th>
							<th>NOM</th>
							<th  style="width:100px"></th>
							<th  style="width:100px"></th>
						</tr>
					</thead>
					<tbody>
						<?php
							if(empty($categ))
							{
						?>
								<tr>
									<td colspan="5">Aucune catégorie d'articles dans magasin dans la base.</td>
								</tr>
						<?php
							}
							else
							{
								foreach($categ as $cat):
						?>
									<tr>
										<td><?= $numligne++;?></td>
										<td><?= $cat->codeTypeA; ?></td>
										<td><?= $cat->designationTypeA ?></td>
										<td>
											<a href="majtypearticle.php?action=edit&code=<?= $cat->codeTypeA; ?>">
												<button class='btn bg-orange'>EDITER</button>
											</a>
										</td>
										<td>
											<a href="majtypearticle.php?action=statut&code=<?= $cat->codeTypeA; ?>">
												<?php
													if($cat->statutTypeA=="ON")
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
							<th  style="width:50px">#</th>
							<th  style="width:100px">CODE</th>
							<th>NOM</th>
							<th  style="width:100px"></th>
							<th  style="width:100px"></th>
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
								<li><a href="majtypearticle.php?page=<?= $i;?>"><?= $i;?></a></li>
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
