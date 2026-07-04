<?php
	require_once('php/session.php');
	require_once('php/fonction.php');
	
	$pagetitle = "GSF | Alertes Stock";
	$pagestitle = "Etat du stock épuisé"; // A remplacer après
	$bcrumb = "Statistique > Alertes Stock";
	
	$actu = date('Y-m-d');
	$obj = "Règlement Facture";
	
	//pagination
	$parpage = 5;
	$result = SQLSelect("SELECT * FROM article WHERE statutArticle='ON' AND seuilArticle>=qteStockArticle");
	$nblignes = $result ? count($result) : 0;
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
	
	ob_start();
?>
<?php
	$articles = SQLSelect("SELECT * FROM article WHERE statutArticle='ON' AND 
	seuilArticle>=qteStockArticle LIMIT :offset, :limit", [':offset' => $first, ':limit' => $parpage]);
?>

	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-filter"></i>
				<h3 class="box-title">Etat du stock épuisé</h3>
			</div>
			<div class="box-body">
				<div class="pull-right">
					<button onclick="javascript:popup_stockepuis();" class="btn btn-primary">
						Générer <i class="fa fa-eye"></i>
					</button>
				</div><br /><br />
				<table class="table table-bordered" name="tabstockepuis" id="tabstockepuis">
					<thead>
						<tr>
							<th style="width:50px">#</th>
							<th>CODE</th>
							<th>DESIGNATION</th>
							<th>STOCK DISPO.</th>
						</tr>
					</thead>
					<tbody>
						<?php
							if(empty($articles))
							{
						?>
								<tr>
									<td colspan="3">Aucun article épuisé.</td>
								</tr>
						<?php
							}
							else
							{
								foreach($articles as $art):
						?>
									<tr>
										<td><?= $numligne++;?></td>
										<td><?= $art->codeArticle; ?></td>
										<td><?= $art->designationArticle; ?></td>
										<td><?= number_format($art->qteStockArticle, 0, ',', ' '); ?></td>
									</tr>
						<?php
								endforeach;
							}
						?>
						
					</tbody>
					<tfoot>
						<tr>
							<th style="width:50px">#</th>
							<th>CODE</th>
							<th>DESIGNATION</th>
							<th>STOCK DISPO.</th>
						</tr>
					</tfoot>
				</table>
				<br>
				<ul class="pagination pagination-sm no-margin pull-right">
					<?php
						for($i=1; $i<=$nbpages; $i++)
						{
					?>
							<li><a href="stockepuis.php?page=<?= $i;?>"><?= $i;?></a></li>
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
