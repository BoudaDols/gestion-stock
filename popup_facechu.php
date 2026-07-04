<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();
	
	$actu = date_format(date_create(date('Y-m-d')), 'Y-m-d');
	
	$sqlf = "SELECT DISTINCT codeFacture,dateFacture,nomClient,solvabiliteFacture 
	FROM facture,client,article WHERE facture_codeTypeF='CREDIT' AND codeClient=facture_codeClient 
	AND codeArticle=facture_codeArticle AND solvabiliteFacture=0";
	$factures = SQLSelect($sqlf);
	$tabech = array();
	foreach($factures as $fact):
		$next = getNextDate($fact->codeFacture);
		if(strtotime($actu)>=strtotime($next))
		{
			$ttc = getTTC($fact->codeFacture);
			$vers = getSumPaidC($fact->codeFacture);
			$solde = $ttc - $vers;
			$tabech[] = array('codef'=>$fact->codeFacture,'clientf'=>$fact->nomClient,
			'datef'=>$fact->dateFacture,'total'=>$ttc,'versf'=>$vers,'solde'=>$solde);
		}
	endforeach;
?>

<!DOCTYPE>
<html>
	<head>
		<meta charset="UTF-8">
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
		<title>Etat des factures crédits échues</title>
		
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
		<table class="table table-bordered" name="tabfechu" id="tabfechu">
			<caption><b><u>ETAT DES FACTURES CREDITS ECHUES</u></b></caption>
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
							if(count($tabech)<=0)
							{
						?>
								<tr>
									<td colspan="7">Aucune facture crédit.</td>
								</tr>
						<?php
							}
							else
							{
								$tsolde = 0;
								$nl = 1;
								foreach($tabech as $fac):
						?>
									<tr>
										<td><?= $nl++;?></td>
										<td><?= $fac['clientf']; ?></td>
										<td><?= $fac['codef']; ?></td>
										<td><?= date_format(date_create($fac['datef']),'d/m/Y'); ?></td>
										<td><?= number_format($fac['total'], 0, ',', ' '); ?></td>
										<td><?= number_format($fac['versf'], 0, ',', ' '); ?></td>
										<td><?= number_format($fac['solde'], 0, ',', ' '); ?></td>
									</tr>
						<?php
								$tsolde += $fac['solde'];
								endforeach;
						?>
								<tr>
									<td>-</td>
									<td>SOLDE TOTAL</td>
									<td>-</td>
									<td>-</td>
									<td>-</td>
									<td>-</td>
									<td><?= number_format($tsolde, 0, ',', ' '); ?></td>
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
		$('#tabfechu').dataTable
		(
			{
				dom: 'Bfrtip',
				buttons:['copy', 'csv', 'excel', 'pdf', 'print']
			}
		);
	} );
</script>