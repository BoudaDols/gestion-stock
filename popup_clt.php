<?php
	require_once('php/session.php');
	require_once('php/fonction.php');
	
	$clts = SQLSelect("SELECT * FROM client WHERE statutClient='ON' ORDER BY codeClient");
	$nl = 1;
?>

<!DOCTYPE>
<html>
	<head>
		<meta charset="UTF-8">
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
		<title>Codes des clients</title>
		
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
			<caption><b><u>CODES DES CLIENTS</u></b></caption>
			<thead>
				<tr>
					<th>#</th>
					<th style="width:150px">Code</th>
					<th>Nom</th>
					<th>Adresse</th>
					<th>Téléphone</th>
					<th>Email</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th>#</th>
					<th style="width:150px">Code</th>
					<th>Nom Client</th>
					<th>Adresse Client</th>
					<th>Téléphone Client</th>
					<th>Email</th>
				</tr>
			</tfoot>
			<tbody>
				<?php
					if(empty($clts))
					{
				?>
						<tr>
							<td colspan="3">Aucun client dans la base</td>
						</tr>
				<?php
					}
					else
					{
						foreach($clts as $clt):
				?>
							<tr>
								<td><?=$nl++;?></td>
								<td><?=$clt->codeClient;?></td>
								<td><?=$clt->nomClient;?></td>
								<td><?=$clt->adresseClient;?></td>
								<td><?=$clt->telClient;?></td>
								<td><?=$clt->emailClient;?></td>
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
				buttons:['excel', 'pdf', 'print']
			}
		);
	} );
</script>