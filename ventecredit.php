<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();

	$pagetitle = "GSF | Vente à crédit";
	$pagestitle = "Vente à crédit"; // A remplacer après
	$bcrumb = "Vente > Vente Crédit";
	
	$msg = "";
	$classmsg = "";
	$button = "";
	
	if(isset($_POST['btnsubmit']) && explode(",",$_POST['listeart'])[0]!=="")
	{//Vérifier si le panier contient un article
		if(isset($_POST['tva']))
		{//TVA appliquée ou pas????
			$tva=1;
		}
		else
		{
			$tva=0;
		}
		$ref = refFact();
		$typef = "CREDIT";
		$listeart = explode(",",$_POST['listeart']);
		$listeprix = explode(",",$_POST['listeprix']);
		$listeqte = explode(",",$_POST['listeqte']);
		$listetot = explode(",",$_POST['listetot']);
		$client = $_POST['client'];
		$modalite = $_POST['modalite'];
		$nbtranche = $_POST['nbtranche'];
		$typerem = $_POST['typerem'];
		$cmd = addslashes($_POST['numcmd']);
		
		$false = 0;//compter le nombre de fois où ya mauvaise qté
		for($i=0;$i<count($listeart);$i++)
		{//comparer la qté en stock à la qté vendue
			$k = getCodeArt($listeart[$i]);
			if($listeqte[$i]>getQte($k))
			{
				$false = $false +1;
			}
		}
		// echo $false; exit;
		
		if($false<>0)
		{
			$msg = "Quantités incorrectes!<br>Quantité 
			vendue supérieure à la quantité en stock.";
			$classmsg = "alert alert-danger";
			$button = "<button type='button' class='close' data-dismiss='alert' 
			aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";
		}
		else
		{
			//---------
			if($typerem=="fixe")
			{//type de remise: montant fixe
				if($_POST['remise']=="")
				{
					$remise = 0;
					for($i=0;$i<count($listeart);$i++)
					{//créer la facture et maj les qtés en stock des articles vendus
						//création de la facture
						$k = getCodeArt($listeart[$i]);;
						$ligne = ($i+1)*100;
						$fact = $bdd->db->PREPARE("INSERT INTO facture (codeFacture,dateFacture,statutFacture,
						quantiteAFacture,solvabiliteFacture,prixVenteFacture,nbRegFacture,remiseFacture,
						ligneFacture,tvaFacture,totalFacture,cmdFacture,facture_codeModalite,facture_codeTypeF,facture_codeClient,facture_codeArticle) 
						VALUES(:codef,:datef,:stat,:qtef,:solvf,:prixf,:regf,:remisef,:lignef,:tva,:totf,:cmd,
						:modalite,:codetypef,:codec,:codea)");
						$fact->EXECUTE(array('codef'=>$ref,'datef'=>date("Y-m-d"),'stat'=>1,'qtef'=>$listeqte[$i],
						'solvf'=>0,'prixf'=>$listeprix[$i],'regf'=>$nbtranche,'remisef'=>$remise,'lignef'=>$ligne,'tva'=>$tva,
						'totf'=>$listetot[$i],'cmd'=>$cmd,'modalite'=>$modalite,'codetypef'=>$typef,'codec'=>$client,'codea'=>$k));
						//maj des qté en stock
						$nqte = getQte($k) - $listeqte[$i];
						$qte = $bdd->db->PREPARE("UPDATE article SET qteStockArticle=:nqte 
						WHERE codeArticle=:code");
						$qte->EXECUTE(array('nqte'=>$nqte,'code'=>$k));
					}
					
					$msg="Vente effectuée avec succès!";
					$classmsg = "alert alert-success";
					$button = "<button type='button' class='close' data-dismiss='alert' 
					aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";
				}
				else
				{
					if(!is_Numeric($_POST['remise']) OR !is_Numeric($_POST['nbtranche']))
					{
						$msg="Vérifiez la saisie du montant ou taux de la remise et du nombre de tranches!<br>
						<input type='button' value='Retour' class='btn btn-info'
						onClick='history.back()'";
						$classmsg = "alert alert-warning";
						$button = "<button type='button' class='close' data-dismiss='alert' 
						aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";
					}
					else
					{
						$remise = $_POST['remise'];
						for($i=0;$i<count($listeart);$i++)
						{//créer la facture et maj les qtés en stock des articles vendus
							//création de la facture
							$k = getCodeArt($listeart[$i]);;
							$ligne = ($i+1)*100;
							$fact = $bdd->db->PREPARE("INSERT INTO facture (codeFacture,dateFacture,statutFacture,
							quantiteAFacture,solvabiliteFacture,prixVenteFacture,nbRegFacture,remiseFacture,
							ligneFacture,tvaFacture,totalFacture,cmdFacture,facture_codeModalite,facture_codeTypeF,facture_codeClient,facture_codeArticle) 
							VALUES(:codef,:datef,:stat,:qtef,:solvf,:prixf,:regf,:remisef,:lignef,:tva,:totf,:cmd,
							:modalite,:codetypef,:codec,:codea)");
							$fact->EXECUTE(array('codef'=>$ref,'datef'=>date("Y-m-d"),'stat'=>1,'qtef'=>$listeqte[$i],
							'solvf'=>0,'prixf'=>$listeprix[$i],'regf'=>$nbtranche,'remisef'=>$remise,'lignef'=>$ligne,'tva'=>$tva,
							'totf'=>$listetot[$i],'cmd'=>$cmd,'modalite'=>$modalite,'codetypef'=>$typef,'codec'=>$client,'codea'=>$k));
							//maj des qté en stock
							$nqte = getQte($k) - $listeqte[$i];
							$qte = $bdd->db->PREPARE("UPDATE article SET qteStockArticle=:nqte 
							WHERE codeArticle=:code");
							$qte->EXECUTE(array('nqte'=>$nqte,'code'=>$k));
						}
						
						$msg="Vente effectuée avec succès!";
						$classmsg = "alert alert-success";
						$button = "<button type='button' class='close' data-dismiss='alert' 
						aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";
					}
				}
			}
			else
			{//type de remise: pourcentage applicable sur le total de la facture
				if($_POST['remise']=="")
				{
					$remise = 0;
					for($i=0;$i<count($listeart);$i++)
					{//créer la facture et maj les qtés en stock des articles vendus
						//création de la facture
						$k = getCodeArt($listeart[$i]);;
						$ligne = ($i+1)*100;
						$fact = $bdd->db->PREPARE("INSERT INTO facture (codeFacture,dateFacture,statutFacture,
						quantiteAFacture,solvabiliteFacture,prixVenteFacture,nbRegFacture,remiseFacture,
						ligneFacture,tvaFacture,totalFacture,cmdFacture,facture_codeModalite,facture_codeTypeF,facture_codeClient,facture_codeArticle) 
						VALUES(:codef,:datef,:stat,:qtef,:solvf,:prixf,:regf,:remisef,:lignef,:tva,:totf,:cmd,
						:modalite,:codetypef,:codec,:codea)");
						$fact->EXECUTE(array('codef'=>$ref,'datef'=>date("Y-m-d"),'stat'=>1,'qtef'=>$listeqte[$i],
						'solvf'=>0,'prixf'=>$listeprix[$i],'regf'=>$nbtranche,'remisef'=>$remise,'lignef'=>$ligne,'tva'=>$tva,
						'totf'=>$listetot[$i],'cmd'=>$cmd,'modalite'=>$modalite,'codetypef'=>$typef,'codec'=>$client,'codea'=>$k));
						//maj des qté en stock
						$nqte = getQte($k) - $listeqte[$i];
						$qte = $bdd->db->PREPARE("UPDATE article SET qteStockArticle=:nqte 
						WHERE codeArticle=:code");
						$qte->EXECUTE(array('nqte'=>$nqte,'code'=>$k));
					}
					
					$msg="Vente effectuée avec succès!";
					$classmsg = "alert alert-success";
					$button = "<button type='button' class='close' data-dismiss='alert' 
					aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";
				}
				else
				{
					if(!is_Numeric($_POST['remise']) OR !is_Numeric($_POST['nbtranche']))
					{
						$msg="Vérifiez la saisie du montant ou taux de la remise et du nombre de tranches!<br>
						<input type='button' value='Retour' class='btn btn-info'
						onClick='history.back()'";
						$classmsg = "alert alert-warning";
						$button = "<button type='button' class='close' data-dismiss='alert' 
						aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";
					}
					else
					{
						$tot=0;
						for($i=0;$i<count($listetot);$i++)
						{//calculer le total pour déterminer la remise
							$tot+= $listetot[$i];
						}
						// $taux = $_POST['remise']/100;
						$remise = round(($tot*$_POST['remise'])/100);//echo $remise; exit;
						for($i=0;$i<count($listeart);$i++)
						{//créer la facture et maj les qtés en stock des articles vendus
							//création de la facture
							$k = getCodeArt($listeart[$i]);;
							$ligne = ($i+1)*100;
							$fact = $bdd->db->PREPARE("INSERT INTO facture (codeFacture,dateFacture,statutFacture,
							quantiteAFacture,solvabiliteFacture,prixVenteFacture,nbRegFacture,remiseFacture,
							ligneFacture,tvaFacture,totalFacture,cmdFacture,facture_codeModalite,facture_codeTypeF,facture_codeClient,facture_codeArticle) 
							VALUES(:codef,:datef,:stat,:qtef,:solvf,:prixf,:regf,:remisef,:lignef,:tva,:totf,:cmd,
							:modalite,:codetypef,:codec,:codea)");
							$fact->EXECUTE(array('codef'=>$ref,'datef'=>date("Y-m-d"),'stat'=>1,'qtef'=>$listeqte[$i],
							'solvf'=>0,'prixf'=>$listeprix[$i],'regf'=>$nbtranche,'remisef'=>$remise,'lignef'=>$ligne,'tva'=>$tva,
							'totf'=>$listetot[$i],'cmd'=>$cmd,'modalite'=>$modalite,'codetypef'=>$typef,'codec'=>$client,'codea'=>$k));
							//maj des qté en stock
							$nqte = getQte($k) - $listeqte[$i];
							$qte = $bdd->db->PREPARE("UPDATE article SET qteStockArticle=:nqte 
							WHERE codeArticle=:code");
							$qte->EXECUTE(array('nqte'=>$nqte,'code'=>$k));
						}
						
						$msg="Vente effectuée avec succès!";
						$classmsg = "alert alert-success";
						$button = "<button type='button' class='close' data-dismiss='alert' 
						aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";
					}
				}
			}
		}
	}
	
	ob_start();
?>

<script type="text/javascript">
	function maj()
	{//remplissage du tableau
		var tableau = document.getElementById('tabvente');
		var trs = tableau.rows;
		var n = trs.length;
		var Cell;
		var codeart = document.getElementById('codeA');
		var article = document.getElementById('designation').value;
		var qte = document.getElementById('qte').value;
		var prix = document.getElementById('prix').value;
		var total = qte * prix;
		var tab = [];
		
		if(qte==""  || prix=="" || isNaN(qte)==true || isNaN(prix)==true)
		{
			alert('Vérifiez la saisie de la quantité et de prix de vente!');
		}
		else
		{
			if(n>1)
			{
				for( var i=1; i<n; i++)
				{
					var element = document.getElementsByTagName('table')[0].getElementsByTagName('tr')[i].cells[1].innerHTML;
					var lgtab = tab.push(element);
				}
			}
			if(tab.indexOf(article) === -1)
			{
				//nouvelle insertion
				var ligne = tableau.insertRow(-1); 
				Cell = ligne.insertCell(0);
				Cell.innerHTML = ligne.rowIndex;					
				Cell = ligne.insertCell(1);
				Cell.innerHTML = article;
				Cell = ligne.insertCell(2); 
				Cell.innerHTML = prix;
				Cell = ligne.insertCell(3); 
				Cell.innerHTML = qte;
				Cell = ligne.insertCell(4); 
				Cell.innerHTML = total;
				Cell = ligne.insertCell(5);
				var bouton = document.createElement("img");
				bouton.src = "dist/img/error.png";
				bouton.onclick = function(){suppression(ligne)};
				Cell.appendChild(bouton);
				//mettre à blanc la quantité et le prix ...
				document.getElementById('qte').value = "";
				document.getElementById('prix').value = "";
				document.getElementById('designation').value = "";
				document.getElementById('qteRestante').value = "";
				document.getElementById('codeA').value = "";
			}
			else if(tab.indexOf(article) > -1)
			{
				//article existe déjà dans le tableau
				alert('Cet article existe déja dans votre pannier!');
			}
			//actualiser les input hidden (liste des articles, quantités & totaux
			leslistes();
		}
		
	}
	
	function suppression(ligne)
	{//suppression d'une ligne du tabkeau
		document.getElementById('tabvente').deleteRow(ligne.rowIndex);
		//Recomptage des lignes...
		var tableau = document.getElementById('tabvente');
		var trs = tableau.rows;
		var n = trs.length;
		var i;
		for (i=1; i<n; i++) //on commence à 1 et non à 0 ;)
		{
			trs[i].cells[0].innerHTML = trs[i].rowIndex;
		}
		//actualiser les input hidden (liste des articles, quantités & totaux
		leslistes();
	}
	
	//tableau des articles
	function lesarticles()
	{
		var tableau = document.getElementById('tabvente');
		var trs = tableau.rows;
		var n = trs.length;
		var tab = [];
		if(n>1)
		{
			for( var i=1; i<n; i++)
			{
				var element = document.getElementsByTagName('table')[0].getElementsByTagName('tr')[i].cells[1].innerHTML;
				var lgtab = tab.push(element);
			}
		} 	
		return tab;
	}
	//tableau des prix
	function lesprix()
	{
		var tableau = document.getElementById('tabvente');
		var trs = tableau.rows;
		var n = trs.length;
		var tab = [];
		if(n>1)
		{
			for( var i=1; i<n; i++)
			{
				var element = document.getElementsByTagName('table')[0].getElementsByTagName('tr')[i].cells[2].innerHTML;
				var lgtab = tab.push(element);
			}
		} 	
		return tab;
	}
	//tableau des quantités
	function lesquantites()
	{
		var tableau = document.getElementById('tabvente');
		var trs = tableau.rows;
		var n = trs.length;
		var tab = [];
		if(n>1)
		{
			for( var i=1; i<n; i++)
			{
				var element = document.getElementsByTagName('table')[0].getElementsByTagName('tr')[i].cells[3].innerHTML;
				var lgtab = tab.push(element);
			}
		} 	
		return tab;
	}
	//tableau des totaux
	function lestotaux()
	{
		var tableau = document.getElementById('tabvente');
		var trs = tableau.rows;
		var n = trs.length;
		var tab = [];
		if(n>1)
		{
			for( var i=1; i<n; i++)
			{
				var element = document.getElementsByTagName('table')[0].getElementsByTagName('tr')[i].cells[4].innerHTML;
				var lgtab = tab.push(element);
			}
		} 	
		return tab;
	}

	
	function leslistes()
	{// tableau = [1,2,3];
		var tabart = lesarticles();
		document.getElementById('listeart').value=tabart;
		var tabprix = lesprix();
		document.getElementById('listeprix').value=tabprix;
		var tabqte = lesquantites();
		document.getElementById('listeqte').value=tabqte;
		var tabtot = lestotaux();
		document.getElementById('listetot').value=tabtot;
	}
	
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
	function prixarticle()
	{
		var xhr = getXhr();
		// On défini ce qu'on va faire quand on aura la réponse
		xhr.onreadystatechange = function()
		{
			// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
			if(xhr.readyState == 4 && xhr.status == 200)
			{
				prix = xhr.responseText;
				// On se sert de innerHTML pour rajouter les options a la liste
				document.getElementById('leprix').innerHTML = prix;
			}
		}
		// Ici faire du post
		xhr.open("POST","ajax/prixarticle.ajax.php",true);
		// ne pas oublier ça pour le post
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		// ne pas oublier de poster les arguments
		// ici, l'id de l'article
		typeSelected = document.getElementById('codeA');
		idart = typeSelected.options[typeSelected.selectedIndex].value;
		xhr.send("idArt="+idart);
	}
	function onInput() {
		var xhr = getXhr();
		// On défini ce qu'on va faire quand on aura la réponse
		xhr.onreadystatechange = function()
		{
			// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
			if(xhr.readyState == 4 && xhr.status == 200)
			{
				prix = xhr.responseText;
				// On se sert de innerHTML pour rajouter les options a la liste
				document.getElementById('leprix').innerHTML = prix;
			}
		}
		// Ici faire du post
		xhr.open("POST","ajax/prixarticle.ajax.php",true);
		// ne pas oublier ça pour le post
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		// ne pas oublier de poster les arguments
		// ici, l'id de l'article
	    var idart = document.getElementById("codeA").value;
	    //idart =  val.options[typeSelected.selectedIndex].value;
    	xhr.send("idArt="+idart);
  }
</script>

	<div class="row-lg-6">
		<section class="col-lg-12 connectSortable">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><b>Sélection des articles</b></h3>
				</div>
				<div class="box-body chat" id="chat-box">
				<!-- Champs input pour les codes barres -->
				<div>
					<div class="row col-lg-12">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-barcode"></i>
								</div>
								<input type="text" class="form-control" style="width:150px" name="codebarre" id="codebarre" placeholder="Code bare"  required/>
							</div>
						</div>
					</div>
				</div>
				<!-- Champs input pour les codes barres -->
				<div class="row col-lg-12">
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon">
								<i class="fa fa-barcode"></i>
							</div>
							<?php
								$sql="SELECT * FROM article WHERE statutArticle='ON'";
								$arts=SQLSelect($sql);
							?>
							<input class="form-control" type="text" style="width:400px" name="codeA" id="codeA" required list="urlarticle" autocomplete="off"  onchange="onInput()">
								<datalist id="urlarticle">
										<select class="form-control" type="text" style="width:400px">
											<?php foreach ($arts as $art):?>
												<option value="<?= $art->codeArticle;?>">
													<b> <?= $art->designationArticle;?></b>
												</option>
											<?php endforeach;?>
										</select>
								</datalist>
							
						</div>
						</div>
					</div>
					<div id="leprix">
						
					</div>
					<div class="row col-lg-3">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-plus-square"></i>
								</div>
								<input type="number" min="0" class="form-control" style="width:150px" name="qte" id="qte" placeholder="Quantité"  required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-4">
						<input class="btn btn-success" type="button" value="Ajouter au panier" onclick="maj()">
					</div>
				</div>
			</div>
		</section>
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
	
	<div class="row">
		<section class="col-lg-12 connectSortable">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title"><b>Articles à vendre</b></h3>
				</div>
				<div class="box-body chat" id="chat-box">
					<table class="table table-bordered" name="tabvente" id="tabvente">
						<tr>
							<th style="width:10px">#</th>
							<th>Article</th>
							<th style="width:150px">Prix Vente</th>
							<th style="width:150px">Quantité</th>
							<th style="width:150px">Total</th>
							<th style="width:20px"></th>
						</tr>
					</table>
					<br>
					<form name="vente" method="POST">
						<div class="row col-lg-12">
							<div class="row col-lg-6">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon">
											<i class="fa fa-barcode"></i>
										</div>
										<?php
											$sql="SELECT * FROM modalite";
											$modals=SQLSelect($sql);
										?>
										<select class="form-control" type="text" style="width:350px" name="modalite" id="modalite" <?=$disabled;?> >
											<!--<option value="-1">Choisir la périodicité</option>-->
											<?php foreach ($modals as $mod):?>
												<option value="<?=$mod->codeModalite;?>">
													<?=$mod->periodiciteModalite;?>
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
											<i class="fa fa-star"></i>
										</div>
										<input type="text" class="form-control" style="width:250px" name="nbtranche" placeholder="Nombre de tranches de paiement" required/>
									</div>
								</div>
							</div>
						</div>
						<div class="row col-lg-12">
							<div class="row col-lg-4">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon">
											<i class="fa fa-minus"></i>
										</div>
										<select class="form-control" style="width:70px" name="typerem">
											<option value="fixe">Fixe</option>
											<option value="pourcent">%</option>
										</select>
										<input type="text" class="form-control" style="width:150px" name="remise" placeholder="Remise à accorder" />
									</div>
								</div>
							</div>
							<div class="row col-lg-2">
								<div class="form-group">
									<div class="input-group">
										<label for="tva"><input type="checkbox" id="tva" name="tva">TVA</label>
									</div>
								</div>
							</div>
							<div class="row col-lg-4">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon">
											<i class="fa fa-barcode"></i>
										</div>
										<?php
											$sql="SELECT * FROM client WHERE statutClient='ON'";
											$clients=SQLSelect($sql);
										?>
										<select class="form-control" type="text" style="width:200px" name="client" id="client" <?=$disabled;?> >
											<option value="0">Tiers</option>
											<?php foreach ($clients as $clt):?>
												<option value="<?=$clt->codeClient;?>">
													<?=$clt->nomClient;?>
												</option>
											<?php endforeach;?>
										</select>
										<button class='btn bg-blue' onclick="javascript:popup();">+</button>
									</div>
								</div>
							</div>
							<div class="row col-lg-2">
								<div class="form-group">
									<div class="input-group">
										<div class="input-group-addon">
											<i class="fa fa-barcode"></i>
										</div>
										<input type="text" class="form-control" style="width:200px" id="numcmd" name="numcmd" placeholder="N° Commande" />
									</div>
								</div>
							</div>
							
							<input type="hidden" name="listeart" id="listeart" value="" >
							<input type="hidden" name="listeprix" id="listeprix" value="" >
							<input type="hidden" name="listeqte" id="listeqte" value="" >
							<input type="hidden" name="listetot" id="listetot" value="" >
							<input type="submit" name="btnsubmit" class="btn btn-success btn-lg btn-block" value="ENREGISTRER LA VENTE">
						</div>
					</form>
				</div>
			</div>
		</section>
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
			window.open('majclient.php','GSF | MAJ Clients','menubar=no, scrollbars=no, top='+top+', left='+left+', width='+width+', height='+height+'');
		}
		-->
	</script>