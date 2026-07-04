<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();
	
	$pagetitle = "GSF | M.A.J des magasins";
	$pagestitle = "Mise à jour des magasins"; // A remplacer après
	$bcrumb = "Paramétrage > M.A.J Magasins";
	$display = "style='display:none'"; //Sert à afficher/cacher le btn 'annuler la modif'
	
	$btnaction = "insert";
	$disabledc = ""; //Sert à griser le code pour update
	$disabled = ""; //Sert à griser les champs après insert ou update
	$tableau = "entier"; //Différencier l'?age du tableau: si tout le tableau ou une recherche
	$msg = "";
	$classmsg = "";
	$button = "";
	$action = "";
	
	$codeM = "";
	$nomM = "";
	
	//Pagination
	$parpage = 10;
	$sql = "SELECT * FROM magasin";
	$nblignes = count(SQLSelect($sql));
	$nbpages = ceil($nblignes/$parpage);
	
	if(isset($_GET['action']))
	{
		$getaction = $_GET['action'];
		$getcode = $_GET['code'];
		
		if($getaction=="edit")
		{
			$sqledit = "SELECT * FROM magasin WHERE codeMagasin='$getcode'";
			$edits = SQLSelect($sqledit);
			foreach($edits as $edit):
				$codeM = $getcode;
				$nomM = stripslashes($edit->libelleMagasin);
			endforeach;
			$btnaction = "update";
			$disabledc = "disabled";
			$display = "style='display:inline'";
		}
		elseif($getaction=="statut")
		{
			$sqlrechstat = "SELECT * FROM magasin WHERE codeMagasin='$getcode'";
			$lestats = SQLSelect($sqlrechstat);
			foreach($lestats as $lestat):
				$oldstat = $lestat->statutMagasin;
			endforeach;
			if($oldstat=="ON")
			{
				//d'abord desactiver les articles en lien
				$sqlstat = $bdd->db->PREPARE("UPDATE magasin SET statutMagasin=:nstat WHERE codeMagasin=:getcode");
				$sqlstat->EXECUTE(array('nstat'=>'OFF', 'getcode'=>$getcode));
				
				
				if($sqlstat)
				{
					$msg="Magasin desactivé!";
					$classmsg = "alert alert-warning";
					$action = "<br><br><br><a href='majmagasin.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabledc = "disabled";
					$disabled = "disabled";
				}
				
			}
			else
			{
				//d'abord activer les articles en lien
				$sqlstat = $bdd->db->PREPARE("UPDATE magasin SET statutMagasin=:nstat WHERE codeMagasin=:getcode");
				$sqlstat->EXECUTE(array('nstat'=>'ON', 'getcode'=>$getcode));
				
				if($sqlstat)
				{
					$msg="Magasin activé!";
					$classmsg = "alert alert-success";
					$action = "<br><br><br><a href='majmagasin.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
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
			
			$codeM = $_POST['codeM'];
			$nomM = addslashes($_POST['nomM']);
			$stat = "ON";
			
			//Vérifier si le même code n'est pas déjà utilisé
			$verifCode = "SELECT * FROM magasin WHERE codeMagasin='$codeM'";
			$result = SQLSelect($verifCode);
			if(!empty($result))
			{
				$msg = "Code déjà attribué à un magasin!<br>Créez-en un autre.";
				$classmsg = "alert alert-info";
				$button = "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";				
			}
			else
			{
				$sql = $bdd->db->PREPARE("INSERT INTO magasin 
										(codeMagasin, libelleMagasin, statutMagasin) 
										VALUES(:code, :nom, :stat)
										");
				$sql->EXECUTE(array('code' => $codeM, 'nom' => $nomM, 'stat' => $stat));
				
				if($sql)
				{
					$msg="Magasin créé avec succès!";
					$classmsg = "alert alert-success";
					$action = "<br><br><br><a href='majmagasin.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabledc = "disabled";
					$disabled = "disabled";
				}
				else
				{
					$msg="Erreur lors de la création du magasin";
					$classmsg = "alert alert-warning";
					$action = "<br><br><br><a href='majmagasin.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabledc = "disabled";
					$disabled = "disabled";
				}
			}
			
		}
		else
		{
			$nomM = addslashes($_POST['nomM']);
			
			$sql = $bdd->db->PREPARE("UPDATE magasin SET libelleMagasin=:nom WHERE codeMagasin=:getcode");
			$sql->EXECUTE(array('nom'=>$nomM,'getcode'=>$getcode));
			
			if($sql)
			{
				$msg="Magasin modifié avec succès!";
				$classmsg = "alert alert-success";
				$action = "<br><br><br><a href='majmagasin.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
				$disabledc = "disabled";
				$disabled = "disabled";
				
				$display = "style='display:none'";
			}
			else
			{
				$msg="Erreur lors de la modification du magasin";
				$classmsg = "alert alert-warning";
				$action = "<br><br><br><a href='majmagasin.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
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
			$sqlrech = "SELECT * FROM magasin LIMIT $first, $parpage";
			$tableau = "entier";
		}
		else
		{
			$sqlrech = "SELECT * FROM magasin WHERE codeMagasin LIKE '%$rech%' OR libelleMagasin LIKE '%$rech%' LIMIT $first, $parpage";
			$tableau = "rechercher";
		}
		
	}
	
	ob_start();
?>
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-sitemap"></i>
				<h3 class="box-title">Ajout/Modification Magasin</h3>
			</div>
			<form name="majclient" method="POST">
				<div class="box-body">
					<div class="row col-lg-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-barcode"></i>
								</div>
								<input type="text" class="form-control" style="width:350px" name="codeM" placeholder="Code du magasin" value="<?= $codeM;?>" <?= $disabledc; ?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-font"></i>
								</div>
								<input type="text" class="form-control" style="width:350px" name="nomM" placeholder="Libellé du magasin" value="<?= $nomM;?>" <?= $disabled; ?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-3"></div>
					<div class="row col-lg-3">
						<input type="hidden" name="btnaction" value="<?= $btnaction; ?>">
						<input type="submit" name="btnsubmit" class="btn btn-primary" value="VALIDER">
					</div>
					<div class="row col-lg-3" <?= $display;?> >
						<a href="majmagasin.php">
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
		$sqlentier = "SELECT * FROM magasin LIMIT $first, $parpage";
		if($tableau=="entier")
		{
			$magasin = SQLSelect($sqlentier);
		}
		else
		{
			$magasin = SQLSelect($sqlrech);
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
							<input type="text" name="research" placeholder="Code/libellé" class="form-control">
							<button class="btn btn-info btn-flat" name="btnresearch" type="submit">Lister</button>
						</form>
					</div>
				</div>
			<div class="box-body">
				
				<table class="table table-bordered" name="tabmagasin" id="tabmagasin">
					<thead>
						<tr>
							<th style="width:50px">#</th>
							<th style="width:100px">CODE</th>
							<th>NOM</th>
							<th style="width:100px"></th>
							<th style="width:100px"></th>
						</tr>
					</thead>
					<tbody>
						<?php
							if(empty($magasin))
							{
						?>
								<tr>
									<td colspan="5">Aucun magasin dans la base.</td>
								</tr>
						<?php
							}
							else
							{
								foreach($magasin as $mag):
						?>
									<tr>
										<td><?= $numligne++;?></td>
										<td><?= $mag->codeMagasin; ?></td>
										<td><?= stripslashes($mag->libelleMagasin) ?></td>
										<td>
											<a href="majmagasin.php?action=edit&code=<?= $mag->codeMagasin; ?>">
												<button class='btn bg-orange'>EDITER</button>
											</a>
										</td>
										<td>
											<a href="majmagasin.php?action=statut&code=<?= $mag->codeMagasin; ?>">
												<?php
													if($mag->statutMagasin=="ON")
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
							<th>NOM</th>
							<th style="width:100px"></th>
							<th style="width:100px"></th>
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
								<li><a href="majmagasin.php?page=<?= $i;?>"><?= $i;?></a></li>
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