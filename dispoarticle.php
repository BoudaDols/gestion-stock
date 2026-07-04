<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();
	
	$pagetitle = "GSF | Disponobilité Article";
	$pagestitle = "Disponibilité d'un article"; // A remplacer après
	$bcrumb = "Stock > M.A.J Stock";
	$display = "style='display:none'"; //Sert à afficher/cacher le btn 'annuler la modif'
	
	$msg = "";
	$classmsg = "";
	$button = "";
	
	if(isset($_POST['btnsubmit']))
	{
		$code = $_POST["code"];
		
		$sqlrech = "SELECT * FROM article WHERE codeArticle LIKE '$code'";
		$arts = SQLSelect($sqlrech);
		// var_dump($arts);exit;
		if(empty($arts))
		{
			$msg = "Vous avez saisi un code introuvable!<br>Rassurer vous que 
			votre code est correct puis ressayer.";
			$classmsg = "alert alert-danger";
			$button = "<button type='button' class='close' data-dismiss='alert' 
			aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";
		}
		else
		{
			$display = "style='display:inline'";
		}
	}
	
	ob_start();
?>

	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-search"></i>
				<h3 class="box-title">Rechercher un article</h3>
			</div>
			<form name="dispoarticle" method="POST">
				<div class="box-body">
					<div class="row col-lg-12">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-barcode"></i>
								</div>
								<input type="text" class="form-control" style="width:150px" name="code" id="code"
								placeholder="Code de l'article" required/>
								<input type="submit" name="btnsubmit" class="btn btn-primary" value="RECHERCHER">
							</div>
						</div>
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
			</div>
		</div>
		<div class="col-lg-3"></div>
	</div>
	
	<div class="row col-lg-12" <?=$display;?>>
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-check"></i>
				<h3 class="box-title">Resultats : <?=$code;?></h3>
			</div>
			<div class="box-body">
				
				<table class="table table-bordered" id="tabarticle">
					<thead>
						<tr>
							<th>CODE</th>
							<th>DESIGNATION</th>
							<th>PRIX MIN</th>
							<th>SEUIL</th>
							<th>STOCK DISPONIBLE</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($arts as $art):
						?>
								<tr>
									<td><?=$art->codeArticle;?></td>
									<td><?=$art->designationArticle;?></td>
									<td><?=number_format($art->prixMinArticle, 0, ',', ' ');?></td>
									<td><?=number_format($art->seuilArticle, 0, ',', ' ');?></td>
									<td><?=number_format($art->qteStockArticle, 0, ',', ' ');?></td>
								</tr>
						<?php 
								endforeach;
						?>
					</tbody>
					<tfoot>
						<tr>
							<th>CODE</th>
							<th>DESIGNATION</th>
							<th>PRIX MIN</th>
							<th>SEUIL</th>
							<th>STOCK DISPONIBLE</th>
						</tr>
					</tfoot>
				</table>
				
			</div>
		</div>
	</div>
	
<?php
	$content = ob_get_clean();
	require_once('patterning.php');
?>