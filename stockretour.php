<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();
	
	$pagetitle = "GSF | Retour des articles";
	$pagestitle = "Retour d'articles vendus"; // A remplacer après
	$bcrumb = "Stock > Retour Articles";
	$display = "style='display:none'"; //Sert à afficher/cacher le btn 'annuler la modif'
	
	$btnaction = "insert";
	$disabledc = ""; //Sert à griser le code pour update
	$disabled = ""; //Sert à griser les champs après insert ou update
	$tableau = "entier"; //Différencier l'?age du tableau: si tout le tableau ou une recherche
	$msg = "";
	$classmsg = "";
	$button = "";
	$action = "";
	
	//Pagination
	$parpage = 10;
	$sql = "SELECT * FROM retour";
	$nblignes = count(SQLSelect($sql));
	$nbpages = ceil($nblignes/$parpage);
	
	if(isset($_GET['action']))
	{
		$getaction = $_GET['action'];
		$getcode = $_GET['code'];
		$getart = $_GET['art'];
		
		if($getaction=="cancel")//
		{
			//réajuster la qté en stock de l'article
			$sql = "SELECT * FROM retour WHERE idRetour='$getcode'";
			$retours = SQLSelect($sql);
			if(!empty($retours))
			{
				foreach($retours as $retour)
				{
					$qteretour = $retour->quantiteARetour;
				}
			}
			$qteStock = getQte($getart) - $qteretour;
			
			//annulation de la mise au rebut de l'article
			$delete = $bdd->db->PREPARE("DELETE FROM retour WHERE idRetour=:idR");
			$delete->EXECUTE(array('idR'=>$getcode));
			
			//mettre à jour la qté en stock de l'article
			$update = $bdd->db->PREPARE("UPDATE article SET qteStockArticle=:nqte WHERE codeArticle=:codeart");
			$update->EXECUTE(array('nqte'=>$qteStock, 'codeart'=>$getart));
			
			$msg="Retour d'article annulé!";
			$classmsg = "alert alert-success";
			$action = "<br><br><br><a href='stockretour.php'><input type='button' 
			class='btn btn-primary' value='NOUVEAU'></a>";
			
			$disabledc = "disabled";
			$disabled = "disabled";
			
		}
	}
	
	if(isset($_POST['btnsubmit']))
	{
		$btnaction = $_POST['btnaction'];
		
		if($btnaction=="insert")
		{
			$codeart = $_POST["codeA"];
			$qte = $_POST["qte"];
			$motif = $_POST["motif"];
			
			$qteStock = getQte($codeart);
				
			$nqteStock = $qteStock + $qte;
				
			$updateart = $bdd->db->PREPARE("UPDATE article SET qteStockArticle=:nqte WHERE codeArticle=:code");
			$updateart->EXECUTE(array('nqte'=>$nqteStock,'code'=>$codeart));
			
			$sql = $bdd->db->PREPARE("INSERT INTO retour (quantiteARetour,dateRetour,motifRetour,retour_codeArticle) 
								VALUES (:qte,:date,:motif,:art)");
			$sql->EXECUTE(array('qte'=>$qte,'date'=>date("Y-m-d"),'motif'=>$motif,'art'=>$codeart));
			
			$msg="Artilce retourné: Stock mis à jour avec succès!";
			$classmsg = "alert alert-success";
			$action = "<br><br><br><a href='stockretour.php'><input type='button' 
			class='btn btn-primary' value='NOUVEAU'></a>";
			
			$disabledc = "disabled";
			$disabled = "disabled";
		}
	}
	
	// Navigation pagination
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
			$sqlrech = "SELECT * FROM retour LIMIT $first, $parpage";
			$tableau = "entier";
		}
		else
		{
			$sqlrech = "SELECT * FROM rebus WHERE retour_codeArticle LIKE '%$rech%' LIMIT $first, $parpage";
			$tableau = "rechercher";
		}
	}
	
	
	ob_start();
?>

<script type="text/javascript">
		function getXhr()
		{
			var xhr = null; 
				if(window.XMLHttpRequest) // Firefox et autres
					xhr = new XMLHttpRequest(); 
					else if(window.ActiveXObject)
					{ // Internet Explorer 
						try 
						{
							xhr = new ActiveXObject("Msxml2.XMLHTTP");
						} catch (e)
						{
							xhr = new ActiveXObject("Microsoft.XMLHTTP");
						}
					}
					else 
					{ // XMLHttpRequest non supporté par le navigateur 
						alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest..."); 
						xhr = false; 
					} 
			return xhr;
		}
		function article()
		{
			var xhr = getXhr();
			// On défini ce qu'on va faire quand on aura la réponse
			xhr.onreadystatechange = function()
			{
				// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
				if(xhr.readyState == 4 && xhr.status == 200)
				{
					articles = xhr.responseText;
					// On se sert de innerHTML pour rajouter les options a la liste
					document.getElementById('codeA').innerHTML = articles;
				}
			}
			// Ici faire du post
			xhr.open("POST","ajax/selectarticle.ajax.php",true);
			// ne pas oublier ça pour le post
			xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
			// ne pas oublier de poster les arguments
			// ici, l'id de la catégorie d'article
			typeSelected = document.getElementById('codeTA');
			idtypea = typeSelected.options[typeSelected.selectedIndex].value;
			xhr.send("idtypeA="+idtypea);
		}
	</script>

	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-trash-o"></i>
				<h3 class="box-title">Retour Articles</h3>
			</div>
			<div class="box-body">
				<form name="majclient" method="POST">
					<div class="row col-lg-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-barcode"></i>
								</div>
								<?php
									$sql="SELECT * FROM typearticle WHERE statutTypeA='ON'";
									$tarts=SQLSelect($sql);
								?>
								<select class="form-control" type="text" style="width:400px" name="codeTA" id="codeTA" onChange="article()" <?=$disabled;?> >
									<option value="-1">Choisir une catégorie</option>
									<?php foreach ($tarts as $tart):?>
										<option value="<?=$tart->codeTypeA;?>">
											<?=$tart->designationTypeA;?>
										</option>
									<?php endforeach;?>
								</select>
							</div>
						</div>
					</div>
					<div class="row col-lg-6">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-barcode"></i>
								</div>
								<select class="form-control" type="text" style="width:400px" name="codeA" id="codeA" <?=$disabled;?> required>
									<option value="-1">Choisir d'abord une catégorie</option>
								</select>
							</div>
						</div>
					</div>
					<div class="row col-lg-4">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-plus-square"></i>
								</div>
								<input type="number" min="0" class="form-control" style="width:150px" name="qte" placeholder="Quantité" <?=$disabled;?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-8">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-font"></i>
								</div>
								<input type="text" class="form-control" style="width:500px" name="motif" placeholder="Motif" <?=$disabled;?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-4"></div>
					<div class="row col-lg-4">
						<input type="hidden" name="btnaction" value="<?= $btnaction; ?>">
						<input type="submit" name="btnsubmit" class="btn btn-primary" value="VALIDER" <?= $disabled;?>>
					</div>
					<div class="row col-lg-4" <?= $display;?> >
						<a href="stockretour.php">
							<input type="button" name="btncancel" class="btn btn-info" value="ANNULER MODIFICATION ">
						</a>
					</div>
				</form>
			</div>
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
	
	<!-- tableau-->
	<?php
		$sqlentier = "SELECT * FROM retour LIMIT $first, $parpage";
		if($tableau=="entier")
		{
			$retours = SQLSelect($sqlentier);
		}
		else
		{
			$retours = SQLSelect($sqlrech);
		}
	?>
	
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-filter"></i>
				<h3 class="box-title">Historique des retours</h3>
			</div>
			<div class="row">
				<div class="col-lg-4"></div>
				<div class="col-lg-4"></div>
				<div class="col-lg-4">
					<form role="form" class="form-inline" name="rebut" action="" method="post">
						<input type="text" name="research" placeholder="Code" class="form-control">
						<button class="btn btn-info btn-flat" name="btnresearch" type="submit">Lister</button>
					</form>
				</div>
			</div>
				<div class="box-body">
					<table class="table table-bordered" name="tabrebut" id="tabrebut">
						<thead>
							<tr>
								<th style="width:50px">#</th>
								<th style="width:150px">CODE ARTICLE</th>
								<th style="width:50px">QUANTITE</th>
								<th>MOTIF</th>
								<th style="width:50px">DATE</th>
								<th style="width:50px"></th>
							</tr>
						</thead>
						<tbody>
							<?php
							if(empty($retours))
							{
							?>
								<tr>
									<td colspan="6">Pas d'article retourné.</td>
								</tr>
							<?php
							}
							else
							{
								foreach($retours as $retour):
							?>
									<tr>
										<td><?= $numligne++;?></td>
										<td><?= $retour->retour_codeArticle; ?></td>
										<td><?= number_format($retour->quantiteARetour, 0, ',', ' '); ?></td>
										<td><?= $retour->motifRetour;?></td>
										<td><?= date_format(date_create($retour->dateRetour),'d/m/Y');?></td>
										<td>
											<a href="stockretour.php?action=cancel&code=<?= $retour->idRetour;?>&art=<?=$retour->retour_codeArticle;?>">
												<button class='btn bg-orange'>ANNULER</button>
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
								<th style="width:150px">CODE ARTICLE</th>
								<th style="width:50px">QUANTITE</th>
								<th>MOTIF</th>
								<th style="width:50px">DATE</th>
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
									<li><a href="stockretour.php?page=<?= $i;?>"><?= $i;?></a></li>
					<?php
							}?>
						</ul>
					<?php	}
					?>
					
				</div>
			</div>
		</div>
	
<?php
	$content = ob_get_clean();
	require_once('patterning.php');
?>