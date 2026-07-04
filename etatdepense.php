<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();
	
	$pagetitle = "GSF | Etat des dépenses journalières";
	$pagestitle = "Etat des dépenses journalières"; // A remplacer après
	$bcrumb = "Statistique > Dépenses du jour";
	
	$actu = date('Y-m-d');
	
	//pagination
	$parpage = 5;
	$sql = "SELECT * FROM reglement WHERE statutReglement='D' AND dateReglement='$actu'";
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
	
	ob_start();
?>
<?php
	$sqlr = "SELECT * FROM reglement WHERE statutReglement='D' 
	AND dateReglement='$actu' LIMIT $first, $parpage";
	$regl = SQLSelect($sqlr);
?>

	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-filter"></i>
				<h3 class="box-title">Etat des dépenses du jour</h3>
			</div>
			<div class="box-body">
				<div class="pull-right">
					<button onclick="javascript:popup_depense();" class="btn btn-primary">
						Générer <i class="fa fa-eye"></i>
					</button>
				</div><br /><br />
				<table class="table table-bordered" name="tabdepense" id="tabdepense">
					<thead>
						<tr>
							<th style="width:50px">#</th>
							<th>OBJET</th>
							<th style="width:100px">MONTANT</th>
						</tr>
					</thead>
					<tbody>
						<?php
							if(empty($regl))
							{
						?>
								<tr>
									<td colspan="3">Aucune dépense.</td>
								</tr>
						<?php
							}
							else
							{
								$sum = 0;
								foreach($regl as $reg):
						?>
									<tr>
										<td><?= $numligne++;?></td>
										<td><?= $reg->objetReglement; ?></td>
										<td><?= number_format($reg->montantReglement, 0, ',', ' '); ?></td>
									</tr>
						<?php
								$sum += $reg->montantReglement;
								endforeach;
						?>
								<tr>
									<td colspan="2">TOTAL</td>
									<td><?= number_format($sum, 0, ',', ' '); ?></td>
								</tr>
						<?php
							}
						?>
						
					</tbody>
					<tfoot>
						<tr>
							<th style="width:50px">#</th>
							<th>OBJET</th>
							<th style="width:100px">MONTANT</th>
						</tr>
					</tfoot>
				</table>
				<br>
				<ul class="pagination pagination-sm no-margin pull-right">
					<?php
						for($i=1; $i<=$nbpages; $i++)
						{
					?>
							<li><a href="etatdepense.php?page=<?= $i;?>"><?= $i;?></a></li>
					<?php
						}
					?>
			</div>
		</div>
	</div>

<?php
	$content = ob_get_clean();
	require_once('patterning.php');
?>

	<script type="text/javascript">
		
		function popup_depense()
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
			window.open('popup_depense.php','GSF | Dépenses journalières','menubar=no, scrollbars=no, top='+top+', left='+left+', width='+width+', height='+height+'');
		}
	</script>