<?php
	require_once('php/session.php');
	require_once('php/fonction.php');
	
	$pagetitle = "GSF | Tableau de bord";
	$pagestitle = " Tableau de bord"; // A remplacer après
	$bcrumb = "Statistique >  Tableau de bord";
	$actu1 = date('Y-m-01');
	$actu2 = date('Y-m-31');
	ob_start();
?>
	
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-bars"></i>
				<h3 class="box-title">Données chiffrées</h3>
			</div>
			<br>
			
			<div class="box-body">
				<div class="row">

					<!--Articles en rupture-->
					<div class="col-md-4">
				        <div class="well">
				          <h4 class="text-primary">
				          	<span class="label label-primary pull-right">
				          		<?php $r = SQLSelect("SELECT * FROM article WHERE statutArticle='ON' AND seuilArticle>=qteStockArticle"); echo $r ? count($r) : 0; ?>
				          	</span> 
				          	<a href="stockepuis.php"> Articles en rupture <a/><br>
				          </h4>
				        </div>
				      </div>
				    <!-- Fin articles en rupture-->

				    <!--Articles de la semaine les 5 articles les plus vendu-->
					<div class="col-md-4">
				        <div class="well">
				          <h4 class="text-primary">
				          	<span class="label label-primary pull-right">
				          		<?php $r = SQLSelect("SELECT f.dateFacture dte, f.facture_codeArticle code, sum(f.quantiteAFacture) qte, a.designationArticle design
													FROM facture f, article a
													WHERE f.dateFacture >= :date1 AND f.dateFacture <= :date2 AND f.facture_codeArticle=a.codeArticle 
													GROUP BY a.designationArticle
													ORDER BY qte DESC
													LIMIT 10", [':date1' => $actu1, ':date2' => $actu2]); echo $r ? count($r) : 0; ?>
				          	</span> 
				          	<a href="listearticleLPvendu.php"> Articles de la semaine<a/><br>
				          </h4>
				        </div>
				      </div>
				    <!-- Fin Articles de la semaine les 5 articles les plus vendu-->

				    <!--Articles de la semaine les 5 articles les plus vendu-->
					<div class="col-md-4">
				        <div class="well">
				          <h4 class="text-primary">
				          	<span class="label label-primary pull-right">
				          		2
				          	</span> 
				          	<a href="listeClientFidele.php"> Nos clients <a/><br>
				          </h4>
				        </div>
				      </div>
				    <!-- Fin Articles de la semaine les 5 articles les plus vendu-->
				    <br><br><br><br><br><br><br>
				    <!--Depense du jour-->
				    <div class="col-md-4">
				        <div class="well">
				          	<h4 class="text-danger">
				          		<span class="label label-danger pull-right">
				          			<?php
										$actu = date('Y-m-d');
										$tdep = 0;
										$regl = SQLSelect("SELECT montantReglement FROM reglement WHERE statutReglement='D' AND 
										dateReglement = :actu", [':actu' => $actu]);
										if(empty($regl))
										{
											echo $tdep." FCFA";
										}
										else
										{
											foreach($regl as $reg):
												$tdep += $reg->montantReglement;
											endforeach;
											echo number_format($tdep, 0, ',', ' ')." FCFA";
										}
									?>
				          		</span> 
				          		<a href="etatdepense.php">Dépenses du jour</a>
				          	</h4>
				        </div>
				    </div>
				    <!--Fin depense du jour -->

				    <!--Recette hors vente jours-->
				    <div class="col-md-4">
				        <div class="well">
				          	<h4 class="text-success">
					          	<span class="label label-success pull-right">
					          		<?php
										$actu = date('Y-m-d');
										$tdep = 0;
										$regl = SQLSelect("SELECT montantReglement FROM reglement WHERE statutReglement='E' AND 
										dateReglement = :actu", [':actu' => $actu]);
										if(empty($regl))
										{
											echo $tdep." FCFA";
										}
										else
										{
											foreach($regl as $reg):
												$tdep += $reg->montantReglement;
											endforeach;
											echo number_format($tdep, 0, ',', ' ')." FCFA";
										}
									?>
					          	</span> 
					          	<a href="etatrecette.php">Entrées HV jours</a>
				      		</h4>
				        </div>
				      </div>
				      <!--Fin recette hors vente jours-->

				      <!--Vente du jour-->
				    <div class="col-md-4">
				        <div class="well">
				          	<h4 class="text-warning">
					          	<span class="label label-warning pull-right">
					          		<?php
										$actu = date('Y-m-d');
										$tvente = 0;
										$vente = SQLSelect("SELECT montantReglement FROM reglement WHERE reglement_codeFacture!='' AND 
										statutReglement!='D' AND statutReglement!='E' AND dateReglement = :actu", [':actu' => $actu]);
										if(empty($vente))
										{
											echo $tvente." FCFA";
										}
										else
										{
											foreach($vente as $vte):
												$tvente += $vte->montantReglement;
											endforeach;
											echo number_format($tvente, 0, ',', ' ')." FCFA";
										}
									?>
					          	</span> 
					          	<a href="etatvente.php">Vente du jour</a>
				      		</h4>
				        </div>
				      </div>
				      <!--Fin Vente du jour-->
				      <br><br><br><br><br><br><br><br><br>
				</div>
			</div>
		</div>
	</div>
	
<?php
	$content = ob_get_clean();
	require_once('patterning.php');
?>
