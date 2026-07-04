<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();
	
	$sql = "SELECT * FROM article WHERE statutArticle='ON' AND seuilArticle>=qteStockArticle";
	$arts = SQLSelect($sql);
	$nl = 1;
?>

<!DOCTYPE>
<html>
	<head>
		<meta charset="UTF-8">
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
		<title>Etat du stock épuisé</title>
		
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="../../js/html5shiv.js"></script>
			<script src="../../js/respond.min.js"></script>
		<![endif]-->
		
		<link rel="stylesheet" type="text/css" href="datatables/jquery.dataTables.min.css" />
		<link rel="stylesheet" type="text/css" href="datatables/bootstrap.css"/>
		<link rel="stylesheet" type="text/css" href="datatables/buttons.dataTables.min.css" />
	</head>
	<body > 
	
	<div class="col-lg-12">
		<br>
		<table class="table table-bordered" name="tabstockepuis" id="tabstockepuis">
			<caption><b><u>ETAT DU STOCK EPUISE</u></b></caption>
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
					if(empty($arts))
					{
				?>
						<tr>
							<td colspan="3">Aucun article épuisé.</td>
						</tr>
				<?php
					}
					else
					{
						foreach($arts as $art):
				?>
							<tr>
								<td><?= $nl++;?></td>
								<td><?= $art->codeArticle; ?></td>
								<td><?= $art->designationArticle; ?></td>
								<td><?= number_format($art->qteStockArticle, 0, ',', ' '); ?></td>
							</tr>
				<?php
						endforeach;
					}
				?>				
			</tbody>
		</table>
	</div>
	
	<script type="text/javascript" src="datatables/jquery-1.12.4.js"></script> 
	<script type="text/javascript" src="datatables/dataTables.js"></script>
	<script type="text/javascript" src="datatables/jquery.dataTables.min.js"></script> 
	<script type="text/javascript" src="datatables/dataTables.buttons.min.js"></script> 
	<script type="text/javascript" src="datatables/buttons.flash.min.js"></script> 
	<script type="text/javascript" src="datatables/jszip.min.js"></script> 
	<script type="text/javascript" src="datatables/pdfmake.min.js"></script> 
	<script type="text/javascript" src="datatables/vfs_fonts.js"></script> 
	<script type="text/javascript" src="datatables/buttons.html5.min.js"></script> 
	<script type="text/javascript" src="datatables/buttons.print.min.js"></script> 
	</body>
</html>

<script type="text/javascript">
	$(document).ready(function() {
		$('#tabstockepuis').dataTable
		(
			{
				dom: 'Bfrtip',
				buttons:['copy', 'csv', 'excel', 'pdf', 'print']
			}
		);
	} );
</script>