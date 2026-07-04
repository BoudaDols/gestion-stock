<?php
	require_once('php/session.php');
	require_once('php/fonction.php');
	
	$pagetitle = "GSF | M.A.J des modalités";
	$pagestitle = "Mise à jour des modalités"; // A remplacer après
	$bcrumb = "Paramétrage > M.A.J Modalités";
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
	$periodeM = "";
	
	//Pagination
	$parpage = 10;
	$result = SQLSelect("SELECT * FROM modalite");
	$nblignes = $result ? count($result) : 0;
	$nbpages = ceil($nblignes/$parpage);
	
	if(isset($_GET['action']))
	{
		$getaction = $_GET['action'];
		$getcode = $_GET['code'];
		
		if($getaction=="edit")
		{
			$edits = SQLSelect("SELECT * FROM modalite WHERE codeModalite = :code", [':code' => $getcode]);
			foreach($edits as $edit):
				$codeM = $getcode;
				$periodeM = $edit->periodiciteModalite;
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
			$codeM = $_POST['codeM'];
			$periodeM = $_POST['periodeM'];
			$stat = "ON";
			
			//Vérifier si le même code n'est pas déjà utilisé
			$result = SQLSelect("SELECT * FROM modalite WHERE codeModalite = :code", [':code' => $codeM]);
			if(!empty($result))
			{
				$msg = "Code déjà attribué à une modalité!<br>Créez-en un autre.";
				$classmsg = "alert alert-info";
				$button = "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";				
			}
			else
			{
				$success = SQLExecute("INSERT INTO modalite 
										(codeModalite, periodiciteModalite) 
										VALUES(:code, :periode)",
										['code' => $codeM, 'periode' => $periodeM]);
				if($success)
				{
					$msg="Modalité créée avec succès!";
					$classmsg = "alert alert-success";
					$action = "<br><br><br><a href='majmodalite.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabledc = "disabled";
					$disabled = "disabled";
				}
				else
				{
					$msg="Erreur lors de la création de la modalité";
					$classmsg = "alert alert-warning";
					$action = "<br><br><br><a href='majmodalite.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabledc = "disabled";
					$disabled = "disabled";
				}
			}
			
		}
		else
		{
			$periodeM = $_POST['periodeM'];
			
			$success = SQLExecute("UPDATE modalite SET periodiciteModalite=:periode
									WHERE codeModalite=:getcode",
									['periode'=>$periodeM,'getcode'=>$getcode]);
			if($success)
			{
				$msg="Modalité modifiée avec succès!";
				$classmsg = "alert alert-success";
				$action = "<br><br><br><a href='majmodalite.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
				
				$disabledc = "disabled";
				$disabled = "disabled";
				$display = "style='display:none'";
			}
			else
			{
				$msg="Erreur lors de la modification de la modalité";
				$classmsg = "alert alert-warning";
				$action = "<br><br><br><a href='majmodalite.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
				
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
				<i class="fa fa-adjust"></i>
				<h3 class="box-title">Ajout/Modification Modalité</h3>
			</div>
			<form name="majclient" method="POST">
				<div class="box-body">
					<div class="row col-lg-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-barcode"></i>
								</div>
								<input type="text" class="form-control" style="width:200px" name="codeM" placeholder="Code" value="<?= $codeM;?>" <?= $disabledc; ?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-font"></i>
								</div>
								<input type="text" class="form-control" style="width:300px" name="periodeM" placeholder="Périodicité" value="<?= $periodeM;?>" <?= $disabled; ?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-3"></div>
					<div class="row col-lg-3">
						<input type="hidden" name="btnaction" value="<?= $btnaction; ?>">
						<input type="submit" name="btnsubmit" class="btn btn-primary" value="VALIDER" <?= $disabled; ?>>
					</div>
					<div class="row col-lg-3" <?= $display;?> >
						<a href="majmodalite.php">
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
			$modalites = SQLSelect("SELECT * FROM modalite LIMIT :offset, :limit", [':offset' => $first, ':limit' => $parpage]);
		}
		else
		{
			$modalites = SQLSelect("SELECT * FROM modalite WHERE codeModalite LIKE :rech OR periodiciteModalite LIKE :rech LIMIT :offset, :limit", [':rech' => "%{$rech}%", ':offset' => $first, ':limit' => $parpage]);
		}
	?>
	
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-filter"></i>
				<h3 class="box-title">Liste des modalités</h3>
			</div>
			<div class="row">
					<div class="col-lg-4"></div>
					<div class="col-lg-4"></div>
					<div class="col-lg-4">
						<form role="form" class="form-inline" name="rechmodalite" action="" method="post">
							<input type="text" name="research" placeholder="Code/Périodité" class="form-control">
							<button class="btn btn-info btn-flat" name="btnresearch" type="submit">Lister</button>
						</form>
					</div>
			</div>
			<div class="box-body">
				<table class="table table-bordered" name="tabmodalite" id="tabmodalite">
					<thead>
						<tr>
							<th style="width:50px">#</th>
							<th style="width:100px">CODE</th>
							<th>PERIODICITE</th>
							<th style="width:100px"></th>
						</tr>
					</thead>
					<tbody>
						<?php
							if(empty($modalites))
							{
						?>
								<tr>
									<td colspan="4">Aucune modalité dans la base.</td>
								</tr>
						<?php
							}
							else
							{
								foreach($modalites as $mod):
						?>
									<tr>
										<td><?= $numligne++;?></td>
										<td><?= $mod->codeModalite; ?></td>
										<td><?= $mod->periodiciteModalite; ?></td>
										<td>
											<a href="majmodalite.php?action=edit&code=<?= $mod->codeModalite; ?>">
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
							<th>PERIODITE</th>
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
								<li><a href="majmodalite.php?page=<?= $i;?>"><?= $i;?></a></li>
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
