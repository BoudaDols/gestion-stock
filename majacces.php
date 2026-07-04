<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();

	$pagetitle = "GSF | Privilèges Accès";
	$pagestitle = "MAJ  des privilèges d'accès"; // A remplacer après
	$bcrumb = "Paramétage > Privilèges Accès";
	
	$style = "style='display:none'";
	$msg = "";
	$classmsg = "";
	$action = "";
	$button = "";
	
	if(isset($_POST['gerer']))
	{
		$style = "style='display:inline'";
		
		$type = $_POST['compte'];
		
		$reqminus = "SELECT * FROM access, menuitem WHERE access_codeSousMenu=codeSousMenu AND access_codeCompte='$type'";
		$reqplus = "SELECT * FROM menuitem WHERE codeSousMenu NOT IN (SELECT codeSousMenu FROM menuitem, access WHERE access_codeSousMenu=codeSousMenu AND access_codeCompte='$type')";
		$plus = SQLSelect($reqplus);
		$minus = SQLSelect($reqminus);
	}
	
	if(isset($_GET['action']) && !empty($_GET['action']))
	{
		$action = $_GET['action'];
		
		if($action=="plus" && isset($_GET['tc']) && !empty($_GET['tc']))
		{
			if(isset($_GET['plus']) && !empty($_GET['plus']))
			{
				if(isset($_GET['tc']) && !empty($_GET['tc']))
				{
					$itemadd = $_GET['plus'];
					$tcompteadd = $_GET['tc'];
					
					$grant = $bdd->db->prepare('INSERT INTO access (access_codeCompte, access_codeSousMenu) VALUES (:typecompte,:item)');
					$grant->execute(array('typecompte'=>$tcompteadd, 'item'=>$itemadd));
					header("location:majacces.php");
				}
				else
					header("location:majacces.php");
			}
				else
					header("location:majacces.php");
		}
		else
		{
			if(isset($_GET['minus']) && !empty($_GET['minus']))
			{
				if(isset($_GET['tc']) && !empty($_GET['tc']))
				{
					$iteminus = $_GET['minus'];
					$tcompteminus = $_GET['tc'];
					
					$revoke = $bdd->db->prepare('DELETE FROM access WHERE access_codeCompte=:typecompte AND access_codeSousMenu=:item');
					$revoke->execute(array('typecompte'=>$tcompteminus, 'item'=>$iteminus));
					header("location:majacces.php");
				}
				else
					header("location:majacces.php");
			}
				else
					header("location:majacces.php");
		}
	}
	
	ob_start();
?>
<section class="content">
	
	<form role="form" class="form-inline" name="majaccess"  action="" method="post">
		<?php
			$reqtype = "SELECT * FROM compte";
			$types = SQLSelect($reqtype);
		?>
		<div class="row">
			<label for="type">Type de compte</label>
			<select class="form-control" name="compte" id="compte">
					<?php foreach($types as $typ):?>
						<option value="<?= $typ->codeCompte; ?>">
							<?= $typ->libelleCompte; ?>
						</option>
					<?php endforeach; ?>
			</select>
			<input type="submit" class="btn btn-primary" name="gerer" value="Gérer">
		</div>
	</form>
	<br />
	<div <?= $style; ?>>
		
		<section class="col-lg-6 connectSortable">
			<div class="box box-primary">
				<div class="box-header">
					<i class="glyphicon glyphicon-minus-sign"></i>
					<h3 class="box-title"><b>Retrait des droits d'accès - <?= $type;?></b></h3>
				</div>
				<div class="box-body chat" id="chat-box">
					<div class="row">
						<form role="form" class="form-inline" name="substract"  action="" method="post">
							<table class="table table-bordered" name="minus" id="minus">
								<thead><tr>
									<th style="width:200px">Privilèges accordés</th>
									<th style="width:10px"></th>
								</tr></thead>
								<tfoot><tr>
									<th style="width:200px">Privilège accordés</th>
									<th style="width:10px"></th>
								</tr></tfoot>
								<tbody>
									<?php 
										if(empty($minus))
										{
									?>
											<tr>
												<td colspan="2">Pas d'accès pour ce compte.</td>
											</tr>
									<?php 
										}
										else
										{
											foreach($minus as $min):
									?>			<tr>
													<td><?= $min->titreSousMenu;?></td>
													<td style="text-align:center">
														<a href="majacces.php?action=minus&minus=<?= $min->codeSousMenu; ?>&tc=<?=$type;?>">
															<input type="button" class="btn btn-danger" value="-">
														</a>
													</td>
												</tr>
									<?php
											endforeach;
										}
									?>
								</tbody>
							</table>
						</form>
					</div>
				</div>
			</div>
		</section>
		
		<section class="col-lg-6 connectSortable">
			<div class="box box-primary">
				<div class="box-header">
					<i class="glyphicon glyphicon-plus-sign"></i>
					<h3 class="box-title"><b>Accord des droits d'accès - <?= $type;?></b></h3>
				</div>
				<div class="box-body chat" id="chat-box">
					<div class="row">
						<form role="form" class="form-inline" name="substract"  action="" method="post">
							<table class="table table-bordered" name="minus" id="minus">
								<thead><tr>
									<th style="width:200px">Privilèges à accorder</th>
									<th style="width:10px"></th>
								</tr></thead>
								<tfoot><tr>
									<th style="width:200px">Privilège à accorder</th>
									<th style="width:10px"></th>
								</tr></tfoot>
								<tbody>
									<?php 
										if(empty($plus))
										{
									?>
											<tr>
												<td colspan="2">Aucun droit de plus à accorder.</td>
											</tr>
									<?php 
										}
										else
										{
											foreach($plus as $pl):
									?>			<tr>
													<td><?= $pl->titreSousMenu;?></td>
													<td style="text-align:center">
														<a href="majacces.php?action=plus&plus=<?= $pl->codeSousMenu; ?>&tc=<?=$type;?>">
															<input type="button" class="btn btn-success" value="+">
														</a>
													</td>
												</tr>
									<?php
											endforeach;
										}
									?>
								</tbody>
							</table>
						</form>
					</div>
				</div>
			</div>
		</section>
		
	</div>
	
</section>

<?php
	$content = ob_get_clean();
	require_once('patterning.php');
?>