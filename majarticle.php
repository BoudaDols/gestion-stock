<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();
	
	$pagetitle = "GSF | M.A.J des articles";
	$pagestitle = "Mise à jour des articles"; // A remplacer après
	$bcrumb = "Paramétrage > M.A.J Articles";
	$display = "style='display:none'"; //Sert à afficher/cacher le btn 'annuler la modif'
	
	$btnaction = "insert";
	$disabledc = ""; //Sert à griser le code pour update
	$disabled = ""; //Sert à griser les champs après insert ou update
	$tableau = "entier"; //Différencier l'?age du tableau: si tout le tableau ou une recherche
	$msg = "";
	$classmsg = "";
	$button = "";
	$action = "";
	
	$codeA = "";
	//$barreA = "";
	$nomA = "";
	$prixA = "";
	$seuilA = "";
	
	//Pagination
	$parpage = 10;
	$sql = "SELECT * FROM article";
	$nblignes = count(SQLSelect($sql));
	$nbpages = ceil($nblignes/$parpage);
	
	if(isset($_GET['action']))
	{
		$getaction = $_GET['action'];
		$getcode = $_GET['code'];
		
		if($getaction=="edit")
		{
			$sqledit = "SELECT * FROM article WHERE codeArticle='$getcode'";
			$edits = SQLSelect($sqledit);
			foreach($edits as $edit):
				$codeA = $getcode;
				//$barreA = $edit->codeBarArticle;
				$nomA = stripslashes($edit->designationArticle);
				$prixA = $edit->prixMinArticle;
				$seuilA = $edit->seuilArticle;
				
			endforeach;
			$btnaction = "update";
			$disabledc = "disabled";
			$display = "style='display:inline'";
		}
		elseif($getaction=="statut")
		{
			$sqlrechstat = "SELECT * FROM article WHERE codeArticle='$getcode'";
			$lestats = SQLSelect($sqlrechstat);
			foreach($lestats as $lestat):
				$oldstat = $lestat->statutArticle;
			endforeach;
			if($oldstat=="ON")
			{
				$sqlstat = $bdd->db->PREPARE("UPDATE article SET statutArticle=:nstat 
				WHERE codeArticle=:getcode");
				$sqlstat->EXECUTE(array('nstat'=>'OFF', 'getcode'=>$getcode));
				
				if($sqlstat)
				{
					$msg="Article desactivé!";
					$classmsg = "alert alert-warning";
					$action = "<br><br><br><a href='majarticle.php'><input type='button' 
					class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabledc = "disabled";
					$disabled = "disabled";
				}
			}
			else
			{
				$sqlstat = $bdd->db->PREPARE("UPDATE article SET statutArticle=:nstat 
				WHERE codeArticle=:getcode");
				$sqlstat->EXECUTE(array('nstat'=>'ON', 'getcode'=>$getcode));
				
				if($sqlstat)
				{
					$msg="Article activé!";
					$classmsg = "alert alert-success";
					$action = "<br><br><br><a href='majarticle.php'><input type='button' 
					class='btn btn-primary' value='NOUVEAU'></a>";
					
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
			$codeA = $_POST['codeA'];
			//$barreA = $_POST['barreA'];
			$nomA = addslashes($_POST['nomA']);
			$prixA = $_POST['prixA'];
			$seuilA = $_POST['seuilA'];
			$categ = $_POST['categ'];
			$stat = "ON";
			
			//Vérifier si le même code n'est pas déjà utilisé
			$verifCode = "SELECT * FROM article WHERE codeArticle='$codeA'";
			$result = SQLSelect($verifCode);
			if(!empty($result))
			{
				$msg = "Code déjà attribué à un article!<br>Créez-en un autre.";
				$classmsg = "alert alert-info";
				$button = "<button type='button' class='close' data-dismiss='alert' 
				aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";				
			}
			else
			{
				if(!is_Numeric($prixA) OR !is_Numeric($seuilA))
				{
					$msg="Vérifiez la saisie du prix et du seuil!<br>
					<input type='button' value='Retour' class='btn btn-info'
					onClick='history.back()'";
					$classmsg = "alert alert-warning";
					$button = "<button type='button' class='close' data-dismiss='alert' 
					aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";
				}
				else
				{
					$sql = $bdd->db->PREPARE("INSERT INTO article 
					(codeArticle, designationArticle,prixMinArticle, seuilArticle, 
					article_codeTypeA, statutArticle) 
					VALUES(:code, :nom, :prix, :seuil, :cat, :stat)");
					$sql->EXECUTE(array('code' => $codeA, 'nom' => $nomA, 'prix'=>$prixA,
										'seuil'=>$seuilA, 'cat'=>$categ, 'stat'=>$stat));
					
					if($sql)
					{
						$msg="Artilce créé avec succès!";
						$classmsg = "alert alert-success";
						$action = "<br><br><br><a href='majarticle.php'><input type='button' 
						class='btn btn-primary' value='NOUVEAU'></a>";
						
						$disabledc = "disabled";
						$disabled = "disabled";
					}
					else
					{
						$msg="Erreur lors de la création de l'article";
						$classmsg = "alert alert-warning";
						$action = "<br><br><br><a href='majarticle.php'><input type='button' 
						class='btn btn-primary' value='NOUVEAU'></a>";
						
						$disabledc = "disabled";
						$disabled = "disabled";
					}
				}
			}
		}
		else
		{
			$nomA = addslashes($_POST['nomA']);
			$prixA = $_POST['prixA'];
			$seuilA = $_POST['seuilA'];
			$categ = $_POST['categ'];
			
			if(!is_Numeric($prixA) OR !is_Numeric($seuilA))
			{
				$msg="Vérifiez la saisie du prix et du seuil!<br>
				<input type='button' value='Retour' class='btn btn-info'
				onClick='history.back()'";
				$classmsg = "alert alert-warning";
				$button = "<button type='button' class='close' data-dismiss='alert' 
				aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";
			}
			else
			{
				$sql = $bdd->db->PREPARE("UPDATE article 
							SET designationArticle=:nom, prixMinArticle=:prix,
							seuilArticle=:seuil, article_codeTypeA=:cat
							WHERE codeArticle=:getcode");
				$sql->EXECUTE(array('nom'=>$nomA, 'prix'=>$prixA, 'seuil'=>$seuilA, 
									'cat'=>$categ, 'getcode'=>$getcode));
				
				if($sql)
				{
					$msg="Article modifié avec succès!";
					$classmsg = "alert alert-success";
					$action = "<br><br><br><a href='majarticle.php'><input type='button' 
					class='btn btn-primary' value='NOUVEAU'></a>";
						
					$disabledc = "disabled";
					$disabled = "disabled";
					
					$display = "style='display:none'";
				}
				else
				{
					$msg="Erreur lors de la modification du magasin";
					$classmsg = "alert alert-warning";
					$action = "<br><br><br><a href='majarticle.php'><input type='button' 
					class='btn btn-primary' value='NOUVEAU'></a>";
						
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
			$sqlrech = "SELECT * FROM article LIMIT $first, $parpage";
			$tableau = "entier";
		}
		else
		{
			$sqlrech = "SELECT * FROM article WHERE codeArticle LIKE '%$rech%' OR 
			designationArticle LIKE '%$rech%' LIMIT $first, $parpage";
			$tableau = "rechercher";
		}
	}

	ob_start();
?>

	
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-cogs"></i>
				<h3 class="box-title">Ajout/Modification Articles</h3>
			</div>
			<form name="majclient" method="POST">
				<div class="box-body">
					<div class="row col-lg-8">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-barcode"></i>
								</div>
								<?php
									$sql="SELECT * FROM typearticle WHERE statutTypeA='ON'";
									$cats=SQLSelect($sql);
								?>
								<select class="form-control" type="text" style="width:350px" name="categ" id="categ" <?= $disabled; ?> >
									<option value="-1">Choisir une catégorie</option>
										<?php foreach ($cats as $ccat):?>
										<option value="<?=$ccat->codeTypeA;?>">
											<?=$ccat->designationTypeA;?>
										</option>
									<?php endforeach;?>
								</select>
							</div>
						</div>
					</div>
					<div class="row col-lg-4">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-barcode"></i>
								</div>
								<input type="text" class="form-control" style="width:200px" name="codeA" placeholder="Code" value="<?= $codeA;?>" <?= $disabledc; ?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-8">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-font"></i>
								</div>
								<input type="text" class="form-control" style="width:500px" name="nomA" placeholder="Désignation" value="<?= $nomA;?>" <?= $disabled; ?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-4">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-usd"></i>
								</div>
								<input type="number" min="0" class="form-control" style="width:200px" name="prixA" placeholder="Prix minimum" value="<?= $prixA;?>" <?= $disabled; ?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-4">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-plus-square"></i>
								</div>
								<input type="number" min="0" class="form-control" style="width:200px" name="seuilA" placeholder="Seuil à ne pas dépasser" value="<?= $seuilA;?>" <?= $disabled; ?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-3"></div>
					<div class="row col-lg-3">
						<input type="hidden" name="btnaction" value="<?= $btnaction; ?>">
						<input type="submit" name="btnsubmit" class="btn btn-primary" value="VALIDER" <?= $disabled; ?>>
					</div>
					<div class="row col-lg-3" <?= $display;?> >
						<a href="majarticle.php">
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
		$sqlentier = "SELECT * FROM article LIMIT $first, $parpage";
		if($tableau=="entier")
		{
			$articles = SQLSelect($sqlentier);
		}
		else
		{
			$articles = SQLSelect($sqlrech);
		}
	?>
	
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-filter"></i>
				<h3 class="box-title">Liste des articles</h3>
			</div>
			<div class="row">
				<div class="col-lg-4"></div>
				<div class="col-lg-4"></div>
				<div class="col-lg-4">
					<form role="form" class="form-inline" name="recharticle" action="" method="post">
						<input type="text" name="research" placeholder="Code/libellé" class="form-control">
						<button class="btn btn-info btn-flat" name="btnresearch" type="submit">Lister</button>
					</form>
				</div>
			</div>
			<div class="box-body">
				<table class="table table-bordered" name="tabarticle" id="tabarticle">
					<thead>
						<tr>
							<th>#</th>
							<th>CATEGORIE</th>
							<th>CODE</th>
							<th>DESIGNATION</th>
							<th>PRIX MIN</th>
							<th>SEUIL</th>
							<th>STOCK</th>
							<th></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php
							if(empty($articles))
							{
						?>
								<tr>
									<td colspan="9">Aucun article dans la base.</td>
								</tr>
						<?php
							}
							else
							{
								foreach($articles as $art):
						?>
									<tr>
										<td><?= $numligne++;?></td>
										<td><?= $art->article_codeTypeA; ?></td>
										<td><?= $art->codeArticle; ?></td>
										<td><?= stripslashes($art->designationArticle) ?></td>
										<td><?= number_format($art->prixMinArticle, 0, ',', ' '); ?></td>
										<td><?= number_format($art->seuilArticle, 0, ',', ' '); ?></td>
										<td><?= number_format($art->qteStockArticle, 0, ',', ' '); ?></td>
										<td>
											<a href="majarticle.php?action=edit&code=<?= $art->codeArticle; ?>">
												<button class='btn bg-orange'>EDITER</button>
											</a>
										</td>
										<td>
											<a href="majarticle.php?action=statut&code=<?= $art->codeArticle; ?>">
												<?php
													if($art->statutArticle=="ON")
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
							<th>CATEGORIE</th>
							<th>CODE</th>
							<th>DESIGNATION</th>
							<th>PRIX MIN</th>
							<th>SEUIL</th>
							<th>STOCK</th>
							<th></th>
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
								<li><a href="majarticle.php?page=<?= $i;?>"><?= $i;?></a></li>
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