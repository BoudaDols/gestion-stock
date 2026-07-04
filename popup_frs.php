<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();
	
	$frss = SQLSelect("SELECT * FROM fournisseur WHERE statutFournisseur='ON' ORDER BY codeFournisseur");
	$nl = 1;
?>

<!DOCTYPE>
<html>
	<head>
		<meta charset="UTF-8">
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
		<title>Codes des fournisseurs</title>
		
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
	<body> 
	
	<div class="col-lg-12">
		<br>
		<table class="table table-bordered" id="codeart">
			<caption><b><u>CODES DES FOURNISSEURS</u></b></caption>
			<thead>
				<tr>
					<th>#</th>
					<th style="width:150px">Code</th>
					<th>Nom</th>
					<th>Adresse</th>
					<th>Téléphone</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th>#</th>
					<th style="width:150px">Code</th>
					<th>Nom Client</th>
					<th>Adresse Client</th>
					<th>Téléphone Client</th>
				</tr>
			</tfoot>
			<tbody>
				<?php
					if(empty($frss))
					{
				?>
						<tr>
							<td colspan="3">Aucun fournisseur dans la base</td>
						</tr>
				<?php
					}
					else
					{
						foreach($frss as $frs):
				?>
							<tr>
								<td><?=$nl++;?></td>
								<td><?=$frs->codeFournisseur;?></td>
								<td><?=$frs->nomFournisseur;?></td>
								<td><?=$frs->adresseFournisseur;?></td>
								<td><?=$frs->telFournisseur;?></td>
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
		$('#codeart').dataTable
		(
			{
				dom: 'Bfrtip',
				buttons:['copy', 'csv', 'excel', 'pdf', 'print']
			}
		);
	} );
</script>