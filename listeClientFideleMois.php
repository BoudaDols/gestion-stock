<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();
	
	$pagetitle = "GSF | Clients";
	$pagestitle = "Clients fidèles du mois"; // A remplacer après
	$bcrumb = "Statistique > Clients fidèles mois";
	ob_start();

	$actu1 = date('Y-m-01');
	$actu2 = date('Y-m-31');

	//pagination
	$parpage = 10;
	$sql = "SELECT sum(r.montantReglement) tot, count(r.reglement_codeFacture) nbreachat, r.reglement_codeFacture codeF, f.facture_codeClient codeC, 			c.nomClient nomC
			from reglement r, facture f, client c
			where f.dateFacture >= '$actu1' AND f.dateFacture <= '$actu2' and r.reglement_codeFacture=f.codeFacture and f.facture_codeClient=c.codeClient
			group by f.facture_codeClient
			order by tot desc";
	$nblignes = count(SQLSelect($sql));
	$nbpages = ceil($nblignes/$parpage);
	
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
	$numligne = 1;
	$sql = "SELECT sum(r.montantReglement) tot, count(r.reglement_codeFacture) nbreachat, r.reglement_codeFacture codeF, f.facture_codeClient codeC, 			c.nomClient nomC
			from reglement r, facture f, client c
			where f.dateFacture >= '$actu1' AND f.dateFacture <= '$actu2' and r.reglement_codeFacture=f.codeFacture and f.facture_codeClient=c.codeClient
			group by f.facture_codeClient
			order by tot desc
			LIMIT $first, $parpage";

			/*SELECT f.dateFacture dte, f.facture_codeArticle code, sum(f.quantiteAFacture) qte, a.designationArticle design
			FROM facture f, article a
			WHERE f.dateFacture >= '$actu1' AND f.dateFacture <= '$actu2' AND f.facture_codeArticle=a.codeArticle 
			GROUP BY a.designationArticle
			ORDER BY qte DESC
			LIMIT 10*/
	$articles = SQLSelect($sql);
?>

	<div class="row col-lg-12">
		<ul class="nav nav-tabs nav-justified">
		 	<li role="presentation" class="active"><a href="listeClientFidele.php">Classement général</a></li>
		 	<li role="presentation" class="active"><a href="listeClientFideleMois.php">Classement du mois en cour</a></li>
		</ul>
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-filter"></i>
				<h3 class="box-title">Nos clients fidèles du <?php echo $actu1 ; ?> au <?php echo $actu2 ; ?> </h3>
			</div>
			<div class="box-body">
				<table class="table table-bordered" name="tabstockepuis" id="tabstockepuis">
					<thead>
						<tr>
							<th style="width:50px">#</th>
							<th>CODE CLIENT</th>
							<th>NOM CLIENT</th>
							<th>NBRE ACHAT</th>
							<th>MONTANT TOTAL</th>
						</tr>
					</thead>
					<tbody>
						<?php
							if(empty($articles))
							{
						?>
								<tr>
									<td colspan="3">Aucun article vendu.</td>
								</tr>
						<?php
							}
							else
							{
								foreach($articles as $art):
						?>
									<tr>
										<td><?= $numligne++;?></td>
										<td><?= $art->codeC; ?></td>
										<td><?= $art->nomC; ?></td>
										<td><?= $art->nbreachat; ?></td>
										<td><?= number_format($art->tot, 0, ',', ' '); ?></td>
									</tr>
						<?php
								endforeach;
							}
						?>
						
					</tbody>
					<tfoot>
						<tr>
							<th style="width:50px">#</th>
							<th>CODE CLIENT</th>
							<th>NOM CLIENT</th>
							<th>NBRE ACHAT</th>
							<th>MONTANT TOTAL</th>
						</tr>
					</tfoot>
				</table>
				<br>
				<ul class="pagination pagination-sm no-margin pull-right">
					<?php
						for($i=1; $i<=$nbpages; $i++)
						{
					?>
							<li><a href="listeClientFidele.php?page=<?= $i;?>"><?= $i;?></a></li>
					<?php
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

	<script type="text/javascript">
		
		function popup_stockepuis()
		{
			width = 1200;
			height = 800;
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
			window.open('popup_stockepuis.php','GSF | Stock épuisé','menubar=no, scrollbars=no, top='+top+', left='+left+', width='+width+', height='+height+'');
		}
	</script>