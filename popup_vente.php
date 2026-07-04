<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();
	
	$actu = date('Y-m-d');
	$sql = "SELECT * FROM reglement,facture,client WHERE reglement_codeFacture!='' AND 
	statutReglement!='D' AND statutReglement!='E' AND dateReglement='$actu' AND codeFacture=reglement_codeFacture 
	AND codeClient=facture_codeClient";
	$regl = SQLSelect($sql);
	$nl = 1;
?>

<!DOCTYPE>
<html>
	<head>
		<meta charset="UTF-8">
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
		<title>Etat des ventes du jour</title>
		
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
		<table class="table table-bordered" name="tabvente" id="tabvente">
		<br><caption><b><u>ETAT DES VENTES DU JOUR</u></b></caption>
			<thead>
				<tr>
					<th style="width:50px">#</th>
					<th>CLIENT</th>
					<th>FACTURE</th>
					<th>MONTANT</th>
				</tr>
			</thead>
			<tbody>
				<?php
					if(empty($regl))
					{
				?>
						<tr>
							<td colspan="4">Aucune vente.</td>
						</tr>
				<?php
					}
					else
					{
						$sum = 0;
						foreach($regl as $reg):
				?>
							<tr>
								<td><?= $nl++;?></td>
								<td><?= $reg->objetReglement; ?></td>
								<td><?= $reg->nomClient; ?></td>
								<td><?= number_format($reg->montantReglement, 0, ',', ' '); ?></td>
							</tr>
				<?php
						$sum += $reg->montantReglement;
						endforeach;
				?>
						<tr>
							<td>-</td>
							<td>TOTAL</td>
							<td>-</td>
							<td><?= number_format($sum, 0, ',', ' '); ?></td>
						</tr>
				<?php
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
		$('#tabvente').dataTable
		(
			{
				dom: 'Bfrtip',
				buttons:['copy', 'csv', 'excel', 'pdf', 'print']
			}
		);
	} );
</script>