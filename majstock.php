<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();
	
	$pagetitle = "GSF | M.A.J du stock";
	$pagestitle = "Mise à jour du stock"; // A remplacer après
	$bcrumb = "Stock > M.A.J Stock";
	$display = "style='display:none'"; //Sert à afficher/cacher le btn 'annuler la modif'
	
	$btnaction = "insert";
	$tableau = "entier"; //Différencier l'?age du tableau: si tout le tableau ou une recherche
	$button = "";
	$msg = "";
	$classmsg = "";
	// $button = "";
	$action = "";
	$disabled = ""; //Sert à griser les champs
	
	$qte ="";
	$prixa = "";
	$prixv = "";
	
	//Pagination
	$parpage = 10;
	$sql = "SELECT * FROM entreestock";
	$nblignes = count(SQLSelect($sql));
	$nbpages = ceil($nblignes/$parpage);
	
	if(isset($_GET['action']))
	{
		$getaction = $_GET['action'];
		$getcode = $_GET['code'];
		$getart = $_GET['art'];
		
		if($getaction=="edit")//
		{
			//déduire la qté de l'entrée de la qté en stock de l'article
			$sql = "SELECT * FROM entreestock WHERE idEntree='$getcode'";
			$entrees = SQLSelect($sql);
			if(!empty($entrees))
			{
				foreach($entrees as $entree)
				{
					$qteentree = $entree->quantiteAEntree;
				}
			}
			
			$qteStock = getQte($getart) - $qteentree;
			
			//copier la ligne à supprimer dans la table drop_entreestock
			$copie = $bdd->db->PREPARE("INSERT INTO drop_entreestock SELECT * FROM entreestock 
										WHERE idEntree=:idEnt");
			$copie->EXECUTE(array('idEnt'=>$getcode));
			
			//mettre à jour la qté en stock de l'article
			$majqte = $bdd->db->PREPARE("UPDATE article SET qteStockArticle=:nqte WHERE codeArticle=:codeart");
			$majqte->EXECUTE(array('nqte'=>$qteStock, 'codeart'=>$getart));
			
			//supprimer la l'entrée en stock de l'article
			$delete = $bdd->db->PREPARE("DELETE FROM entreestock WHERE idEntree=:idEntree");
			$delete->EXECUTE(array('idEntree'=>$getcode));
			
			$disabled = "disabled";
			$msg = "Stock mis à jour avec succès!";
			$classmsg = "alert alert-success";
			// $button = "<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>
							// <i class='glyphicon glyphicon-off'></i></button>";
			$action = "<br><br><br><a href='majstock.php'><input type='button' class='btn btn-primary' value='NOUVEAU'></a>";
			// var_dump($qteStock); exit;
		}
	}
	
	if(isset($_POST['btnsubmit']))
	{
		$btnaction = $_POST['btnaction'];
		
		if($btnaction=="insert")
		{
			$art = $_POST['codeA'];
			$frs = $_POST['codeF'];
			$qte = $_POST['qte'];
			$prixa = $_POST['prixa'];
			$prixv = $_POST['prixv'];
			
			if($prixv=="")
				$req = "majqte";// si prix de vente pas saisi on maj la qté seulement de l'article
			else
				$req = "majprixqte";//sinon on maj le prix et la qté de l'article
			
			$qteStock = getQte($art) + $qte;
			
			if($req=="majqte")
			{
				if(!is_Numeric($prixa) OR !is_Numeric($qte))
				{
					$msg="Vérifiez la saisie du prix d'achat et de la quantité!<br>
					<input type='button' value='Retour' class='btn btn-info'
					onClick='history.back()'";
					$classmsg = "alert alert-warning";
					$button = "<button type='button' class='close' data-dismiss='alert' 
					aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";
				}
				else
				{
					$sql = $bdd->db->PREPARE("INSERT INTO entreestock (entree_codeArticle,
									entree_codeFournisseur,quantiteAEntree,dateEntree,prixAchatEntree) 
							VALUES(:codea,:codef,:qte,:date,:pa)");
					$sql->EXECUTE(array('codea'=>$art,'codef'=>$frs,'qte'=>$qte,
									'date'=>date("Y-m-d"),'pa'=>$prixa));
					
					$sqlprix = $bdd->db->PREPARE("UPDATE article SET qteStockArticle=:nqte WHERE codeArticle=:codeart");
					$sqlprix->EXECUTE(array('nqte'=>$qteStock, 'codeart'=>$art));
					
					$disabled = "disabled";
					$msg="Stock mis à jour avec succès!";
					$classmsg = "alert alert-success";
					$action = "<br><br><br><a href='majstock.php'><input type='button' 
					class='btn btn-primary' value='NOUVEAU'></a>";
				}
			}
			else
			{
				if(!is_Numeric($prixa) OR !is_Numeric($qte))
				{
					$msg="Vérifiez la saisie du prix d'achat et de la quantité!<br>
					<input type='button' value='Retour' class='btn btn-info'
					onClick='history.back()'";
					$classmsg = "alert alert-warning";
					$button = "<button type='button' class='close' data-dismiss='alert' 
					aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";
				}
				else
				{
					$sql = $bdd->db->PREPARE("INSERT INTO entreestock (entree_codeArticle,
									entree_codeFournisseur,quantiteAEntree,dateEntree,prixAchatEntree) 
							VALUES(:codea,:codef,:qte,:date,:pa)");
					$sql->EXECUTE(array('codea'=>$art,'codef'=>$frs,'qte'=>$qte,
									'date'=>date("Y-m-d"),'pa'=>$prixa));
					
					$sqlprix = $bdd->db->PREPARE("UPDATE article SET qteStockArticle=:nqte, prixMinArticle=:nprix 
												WHERE codeArticle=:codeart");
					$sqlprix->EXECUTE(array('nqte'=>$qteStock, 'nprix'=>$prixv, 'codeart'=>$art));
					
					$disabled = "disabled";
					$msg="Stock mis à jour avec succès!";
					$classmsg = "alert alert-success";
					$action = "<br><br><br><a href='majstock.php'><input type='button' 
					class='btn btn-primary' value='NOUVEAU'></a>";
				}
			}
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
			$sqlrech = "SELECT * FROM entreestock LIMIT $first, $parpage";
			$tableau = "entier";
		}
		else
		{
			$sqlrech = "SELECT * FROM entreestock WHERE entree_codeArticle LIKE '%$rech%' LIMIT $first, $parpage";
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
				<i class="fa fa-flag"></i>
				<h3 class="box-title">Ajout/Modification Stock</h3>
			</div>
			<form name="majstock" method="POST">
				<div class="box-body">
					<div class="row col-lg-12">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-barcode"></i>
								</div>
								<?php
									$sql="SELECT * FROM fournisseur WHERE statutFournisseur='ON'";
									$fourn=SQLSelect($sql);
								?>
								<select class="form-control" type="text" style="width:400px" name="codeF" id="codeF" <?=$disabled;?> >
									<option value="0">Choisir un fournisseur</option>
									<?php foreach ($fourn as $frs):?>
										<option value="<?=$frs->codeFournisseur;?>">
											<?=$frs->nomFournisseur;?>
										</option>
									<?php endforeach;?>
								</select>
								<button class='btn bg-blue' onclick="javascript:popup();">+</button>
							</div>
						</div>
					</div>
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
									<!--<option value="-1">Choisir d'abord une catégorie</option>-->
								</select>
							</div>
						</div>
					</div>
					<div class="row col-lg-3">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-plus-square"></i>
								</div>
								<input type="number" min="0" class="form-control" style="width:150px" name="qte" placeholder="Quantité" value="<?=$qte;?>" <?=$disabled;?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-3">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-money"></i>
								</div>
								<input type="number" min="0" class="form-control" style="width:150px" name="prixa" placeholder="Prix Achat" value="<?=$prixa;?>" <?=$disabled;?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-3">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-money"></i>
								</div>
								<input type="number" min="0" class="form-control" style="width:150px" name="prixv" placeholder="Prix Min Vente" value="<?=$prixv;?>" <?=$disabled;?> />
							</div>
						</div>
					</div>
					<div class="row col-lg-3"></div>
					<div class="row col-lg-3">
						<input type="hidden" name="btnaction" value="<?= $btnaction; ?>">
						<input type="submit" name="btnsubmit" class="btn btn-primary" value="VALIDER" <?= $disabled;?>>
					</div>
					<div class="row col-lg-3" <?= $display;?> >
						<a href="majstock.php">
							<input type="button" name="btncancel" class="btn btn-info" value="ANNULER MODIFICATION ">
						</a>
					</div>
					<div class="row col-lg-3"></div>
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
	
	<!-- tableau-->
	<?php
		$sqlentier = "SELECT * FROM entreestock LIMIT $first, $parpage";
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
				<h3 class="box-title">Historique MAJ du stock</h3>
			</div>
			<div class="row">
				<div class="col-lg-4"></div>
				<div class="col-lg-4"></div>
				<div class="col-lg-4">
					<form role="form" class="form-inline" name="stock" action="" method="post">
						<input type="text" name="research" placeholder="Code/libellé" class="form-control">
						<button class="btn btn-info btn-flat" name="btnresearch" type="submit">Lister</button>
					</form>
				</div>
			</div>
			<div class="box-body">
				<table class="table table-bordered" name="tabmajstock" id="tabmajstock">
					<thead>
						<tr>
							<th style="width:50px">#</th>
							<th>ARTICLE</th>
							<th style="width:50px">QUANTITE</th>
							<th style="width:100px">ACHAT</th>
							<th style="width:50px">DATE</th>
							<th style="width:50px"></th>
						</tr>
					</thead>
					<tbody>
						<?php
							if(empty($articles))
							{
						?>
								<tr>
									<td colspan="6">Aucun article en stock.</td>
								</tr>
						<?php
							}
							else
							{
								foreach($articles as $art):
						?>
									<tr>
										<td><?= $numligne++;?></td>
										<td><?= $art->entree_codeArticle; ?></td>
										<td><?= number_format($art->quantiteAEntree, 0, ',', ' '); ?></td>
										<td><?= number_format($art->prixAchatEntree, 0, ',', ' ');?></td>
										<td><?= date_format(date_create($art->dateEntree),'d/m/Y');?></td>
										<td>
											<a href="majstock.php?action=edit&code=<?= $art->idEntree;?>&art=<?=$art->entree_codeArticle;?>">
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
							<th>ARTICLE</th>
							<th style="width:50px">QUANTITE</th>
							<th style="width:100px">ACHAT</th>
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
								<li><a href="majstock.php?page=<?= $i;?>"><?= $i;?></a></li>
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

	<script type="text/javascript">
		<!--
		function popup()
		{
			width = 1200;
			height = 700;
			if(window.innerWidth)
			{
				var left = (window.innerWidth-width)/2;
				var top = (window.innerHeight-height)/2;
			}
			else
			{
				var left = (document.body.clientWidth-width)/2;
				var top = (document.body.clientHeight-height)/2;
			}
			window.open('majfournisseur.php','GSF | MAJ Fournisseurs','menubar=no, scrollbars=no, top='+top+', left='+left+', width='+width+', height='+height+'');
		}
		-->
	</script>