<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();
	
	$sqlf = "SELECT DISTINCT codeFacture,dateFacture,nomClient,solvabiliteFacture 
	FROM facture,client,article WHERE facture_codeTypeF='CREDIT' AND codeClient=facture_codeClient 
	AND codeArticle=facture_codeArticle AND solvabiliteFacture=0";
	$factures = SQLSelect($sqlf);
	$nl = 1;
?>

<!DOCTYPE>
<html>
	<head>
		<meta charset="UTF-8">
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
		<title>Etat des factures crédits non soldées</title>
		
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
		<table class="table table-bordered" name="tabfcredit" id="tabfcredit">
			<caption><b><u>LISTE DES FACTURES CREDITS</u></b></caption>
			<thead>
				<tr>
					<th style="width:50px">#</th>
					<th>CLIENT</th>
					<th style="width:100px">FACTURE</th>
					<th style="width:50px">DATE</th>
					<th style="width:100px">TOTAL</th>
					<th style="width:100px">TOT. VERSE</th>
					<th style="width:100px">SOLDE</th>
				</tr>
			</thead>
			<tbody>
				<?php
					if(empty($factures))
					{
				?>
						<tr>
							<td colspan="7">Aucune facture crédit.</td>
						</tr>
				<?php
					}
					else
					{
						$solde = 0;
						foreach($factures as $fact):
				?>
							<tr>
								<td><?= $nl++;?></td>
								<td><?= $fact->nomClient; ?></td>
								<td><?= $fact->codeFacture; ?></td>
								<td><?= date_format(date_create($fact->dateFacture),'d/m/Y'); ?></td>
								<td><?= number_format(getTTC($fact->codeFacture), 0, ',', ' '); ?></td>
								<td><?= number_format(getSumPaidC($fact->codeFacture), 0, ',', ' '); ?></td>
								<td><?= number_format((getTTC($fact->codeFacture)-getSumPaidC($fact->codeFacture)), 0, ',', ' '); ?></td>
							</tr>
				<?php
						$solde += (getTTC($fact->codeFacture)-getSumPaidC($fact->codeFacture));
						endforeach;
				?>
						<tr>
							<td>-</td>
							<td><b>SOLDE TOTAL</b></td>
							<td>-</td>
							<td>-</td>
							<td>-</td>
							<td>-</td>
							<td><?= number_format($solde, 0, ',', ' '); ?></td>
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
		$('#tabfcredit').dataTable
		(
			{
				dom: 'Bfrtip',
				buttons:['copy', 'csv', 'excel', 'pdf', 'print']
			}
		);
	} );
</script>