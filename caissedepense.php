<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();
	
	$pagetitle = "GSF | Dépenses";
	$pagestitle = "Mise à jour des dépenses"; // A remplacer après
	$bcrumb = "Caisse > Dépenses";
	$display = "style='display:none'"; //Sert à afficher/cacher le btn 'annuler la modif'
	
	$btnaction = "insert";
	$disabled = ""; //Sert à griser les champs après insert ou update
	$tableau = "entier"; //Différencier l'?age du tableau: si tout le tableau ou une recherche
	$msg = "";
	$classmsg = "";
	$button = "";
	$action = "";
	
	$montant = "";
	$objet = "";
	
	//Pagination
	$parpage = 10;
	$sql = "SELECT * FROM reglement WHERE statutReglement='D' AND reglement_codeFacture=''";
	$nblignes = count(SQLSelect($sql));
	$nbpages = ceil($nblignes/$parpage);
	
	if(isset($_GET['action']))
	{
		$getaction = $_GET['action'];
		$getid = $_GET['id'];
		
		if($getaction=="edit")
		{
			$sqledit = "SELECT * FROM reglement WHERE idReglement='$getid'";
			$edits = SQLSelect($sqledit);
			foreach($edits as $edit):
				$montant = stripslashes($edit->montantReglement);
				$objet = $edit->objetReglement;
			endforeach;
			$btnaction = "update";
			$disabledc = "disabled";
			$display = "style='display:inline'";
		}	
	}
	
	if(isset($_POST['btnsubmit']))
	{
		$btnaction = $_POST['btnaction'];
		
		$montant = $_POST['montant'];
		$objet = addslashes($_POST['objet']);
		
		if($btnaction=="insert")
		{
			if(!is_Numeric($montant))
			{
				$msg="Vérifiez la saisie du montant de la dépense!<br>
				<input type='button' value='Retour' class='btn btn-info'
				onClick='history.back()'";
				$classmsg = "alert alert-warning";
				$button = "<button type='button' class='close' data-dismiss='alert' 
				aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";
			}
			else
			{
				$sql = $bdd->db->PREPARE("INSERT INTO reglement (dateReglement,montantReglement,objetReglement,
							statutReglement,reglement_codeFacture) 
							VALUES(:date,:mtt,:obj,:stat,:fac)
									");
				$sql->EXECUTE(array('date'=>date("Y-m-d"),'mtt'=>$montant,'obj'=>$objet,
							'stat'=>'D','fac'=>'',));
				if($sql)
				{
					$msg="Caisse débitée!";
					$classmsg = "alert alert-success";
					$action = "<br><br><br><a href='caissedepense.php'><input type='button' 
					class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabled = "disabled";
				}
				else
				{
					$msg="Erreur lors de l'opération de caisse";
					$classmsg = "alert alert-warning";
					$action = "<br><br><br><a href='caissedepense.php'><input type='button' 
					class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabled = "disabled";
				}
			}
			
		}
		else
		{
			if(!is_Numeric($montant))
			{
				$msg="Vérifiez la saisie du montant de la dépense!<br>
				<input type='button' value='Retour' class='btn btn-info'
				onClick='history.back()'";
				$classmsg = "alert alert-warning";
				$button = "<button type='button' class='close' data-dismiss='alert' 
				aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";
			}
			else
			{
				$sql = $bdd->db->PREPARE("UPDATE reglement SET montantReglement=:mtt,
				objetReglement=:obj WHERE idReglement=:id");
				$sql->EXECUTE(array('mtt'=>$montant,'obj'=>$objet,'id'=>$getid));
				if($sql)
				{
					$msg="MAJ effectuée!";
					$classmsg = "alert alert-success";
					$action = "<br><br><br><a href='caissedepense.php'><input type='button' 
					class='btn btn-primary' value='NOUVEAU'></a>";
					
					$disabled = "disabled";
					$display = "style='display:none'";
				}
				else
				{
					$msg="Erreur lors de l'opération de caisse";
					$classmsg = "alert alert-warning";
					$action = "<br><br><br><a href='caissedepense.php'><input type='button' 
					class='btn btn-primary' value='NOUVEAU'></a>";
					
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
			$sqlrech = "SELECT * FROM reglement WHERE statutReglement='D' AND reglement_codeFacture='' LIMIT $first, $parpage";
			$tableau = "entier";
		}
		else
		{
			$sqlrech = "SELECT * FROM reglement WHERE statutReglement='D' AND reglement_codeFacture='' AND objetReglement LIKE '%$rech%' LIMIT $first, $parpage";
			$tableau = "rechercher";
		}
		
	}
	
	ob_start();
?>
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-adjust"></i>
				<h3 class="box-title">Retrait de caisse</h3>
			</div>
			<form name="entreecaisse" method="POST">
				<div class="box-body">
					<div class="row col-lg-4">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-dollar"></i>
								</div>
								<input type="text" class="form-control" style="width:200px" name="montant" placeholder="Montant" value="<?= $montant;?>" <?= $disabled; ?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-8">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-font"></i>
								</div>
								<input type="text" class="form-control" style="width:450px" name="objet" placeholder="Objet" value="<?= $objet;?>" <?= $disabled; ?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-3"></div>
					<div class="row col-lg-3">
						<input type="hidden" name="btnaction" value="<?= $btnaction; ?>">
						<input type="submit" name="btnsubmit" class="btn btn-primary" value="VALIDER" <?= $disabled; ?>>
					</div>
					<div class="row col-lg-3" <?= $display;?> >
						<a href="caissedepense.php">
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
		$sqlentier = "SELECT * FROM reglement WHERE statutReglement='D' AND 
		reglement_codeFacture='' LIMIT $first, $parpage";
		if($tableau=="entier")
		{
			$entrees = SQLSelect($sqlentier);
		}
		else
		{
			$entrees = SQLSelect($sqlrech);
		}
	?>
	
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-filter"></i>
				<h3 class="box-title">Dépenses</h3>
			</div>
			<div class="row">
					<div class="col-lg-4"></div>
					<div class="col-lg-4"></div>
					<div class="col-lg-4">
						<form role="form" class="form-inline" name="rechentree" action="" method="post">
							<input type="text" name="research" placeholder="Objet" class="form-control">
							<button class="btn btn-info btn-flat" name="btnresearch" type="submit">Lister</button>
						</form>
					</div>
			</div>
			<div class="box-body">
				<table class="table table-bordered" name="tabentree" id="tabentree">
					<thead>
						<tr>
							<th style="width:50px">#</th>
							<th style="width:100px">DATE</th>
							<th style="width:100px">MONTANT</th>
							<th>OBJET</th>
							<th style="width:100px"></th>
						</tr>
					</thead>
					<tbody>
						<?php
							if(empty($entrees))
							{
						?>
								<tr>
									<td colspan="4">Aucune sortie de caisse.</td>
								</tr>
						<?php
							}
							else
							{
								foreach($entrees as $ent):
						?>
									<tr>
										<td><?= $numligne++;?></td>
										<td><?= date_format(date_create($ent->dateReglement),'d/m/Y'); ?></td>
										<td><?= number_format($ent->montantReglement, 0, ',', ' '); ?></td>
										<td><?= $ent->objetReglement; ?></td>
										<td>
											<a href="caissedepense.php?action=edit&id=<?= $ent->idReglement; ?>">
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
							<th style="width:100px">DATE</th>
							<th style="width:100px">MONTANT</th>
							<th>OBJET</th>
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
								<li><a href="caissedepense.php?page=<?= $i;?>"><?= $i;?></a></li>
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