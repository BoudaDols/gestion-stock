<?php
	require_once('php/session.php');
	require_once('php/fonction.php');
	
	$pagetitle = "GSF | Alertes Stock";
	$pagestitle = "Produits les plus vendus"; // A remplacer après
	$bcrumb = "Statistique > Alertes Stock";
	
	ob_start();
?>
<?php
	$numligne = 1;
	$actu1 = date('Y-m-01');
	$actu2 = date('Y-m-31');
	$articles = SQLSelect("SELECT f.dateFacture dte, f.facture_codeArticle code, sum(f.quantiteAFacture) qte, a.designationArticle design
			FROM facture f, article a
			WHERE f.dateFacture >= :date1 AND f.dateFacture <= :date2 AND f.facture_codeArticle=a.codeArticle 
			GROUP BY a.designationArticle
			ORDER BY qte DESC
			LIMIT 10", [':date1' => $actu1, ':date2' => $actu2]);
?>

	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-filter"></i>
				<h3 class="box-title">Produits les plus vendus</h3>
			</div>
			<div class="box-body">
				<table class="table table-bordered" name="tabstockepuis" id="tabstockepuis">
					<thead>
						<tr>
							<th style="width:50px">#</th>
							<th>DATE</th>
							<th>CODE</th>
							<th>DESIGNATION</th>
							<th>QTE VENDUE</th>
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
										<td><?= $art->dte; ?></td>
										<td><?= $art->code; ?></td>
										<td><?= $art->design; ?></td>
										<td><?= number_format($art->qte, 0, ',', ' '); ?></td>
									</tr>
						<?php
								endforeach;
							}
						?>
						
					</tbody>
					<tfoot>
						<tr>
							<th style="width:50px">#</th>
							<th>DATE</th>
							<th>CODE</th>
							<th>DESIGNATION</th>
							<th>QTE VENDUE</th>
						</tr>
					</tfoot>
				</table>
				<br>
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
