<?php
	require_once('php/session.php');
	require_once('php/fonction.php');
	require_once('php/ChiffresEnLettres.php');
	$lettre = new ChiffreEnLettre();
	
	$pagetitle = "GSF | Ventes validées";
	$pagestitle = "Ticket de caisse : Ventes validées"; // A remplacer après
	$bcrumb = "Vente > Impression";
	
	$logo = getLogo();
	$nom = getNom();
	$adr = getAdr();
	$tel = getTel1()." / ".getTel2();
	$bank = getBank();
	$rccm = getRCCM();
	$ifu= getIFU();
	
	if(isset($_GET['codefact']))
	{
		$code = $_GET['codefact'];
		$facts = SQLSelect("SELECT DISTINCT facture_codeClient, remiseFacture, dateFacture, facture_codeTypeF, 
		cmdFacture FROM facture WHERE codeFacture = :code", [':code' => $code]);
		if(!empty($facts))
		{
			foreach($facts as $fact):
				$client = $fact->facture_codeClient;	
				$date = $fact->dateFacture;
				$remise = $fact->remiseFacture;
				$type = $fact->facture_codeTypeF;
				$cmd = $fact->cmdFacture;
			endforeach;
		}
		$arts = SQLSelect("SELECT * FROM facture WHERE codeFacture = :code", [':code' => $code]);
	}

	 ob_start();	
	?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php if(isset($pagetitle)) echo $pagetitle; else echo "ACCUEIL | GSF"; ?></title>
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport" charset="UTF-8" />
		<link rel="shortcut icon" type="image/x-icon" href="dist/img/gsf_logo.jpg" />
		<!-- Bootstrap 3.3.2 -->
		<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />    
		<!-- FontAwesome 4.3.0 -->
		<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
		<!-- Ionicons 2.0.0 -->
		<link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />    
		<!-- Theme style -->
		<link href="dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />    
		<!-- AdminLTE Skins. Choose a skin from the css/skins 
         folder instead of downloading all of them to reduce the load. -->
		<link href="dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
		<!-- iCheck -->
		<link href="plugins/iCheck/flat/blue.css" rel="stylesheet" type="text/css" />
		<!-- Morris chart -->
		<link href="plugins/morris/morris.css" rel="stylesheet" type="text/css" />
		<!-- jvectormap -->
		<link href="plugins/jvectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
		<!-- Date Picker -->
		<link href="plugins/datepicker/datepicker3.css" rel="stylesheet" type="text/css" />
		<!-- Daterange picker -->
		<link href="plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
		<!-- bootstrap wysihtml5 - text editor -->
		<link href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
		<![endif]-->
		
	</head>
	<body class="skin-blue layout-boxed sidebar-min">
		<!-- content -->

		<div class="content">
			<span style="font-size:8px; " >
			<table  width="30px">
				<tr><td><img src="<?=$logo;?>" alt="Cave Tene" height="20" width="20" /></td></tr>
				<tr><td><img src="dist/img/bas_logo.jpg" alt="Cave Tene" /></td></tr>
				<tr><td><b><?=$nom;?></b></td></tr>
				<!--<tr><td><?=$adr;?><?=" --- ".$tel;?></td></tr>
				<tr><td>BANQUE: <?=$bank;?></td></tr>
				<tr><td>RCCM: <?=$rccm;?></td></tr>
				<tr><td>IFU: <?=$ifu;?></td></tr>-->
				<tr><td style="text-align:right">Ouagadougou, le <?=date_format(date_create($date),'d/m/Y');?></td></tr>
			</table>
			<?php
				$clts = SQLSelect("SELECT * FROM client WHERE codeClient = :client", [':client' => $client]);
				foreach($clts as $clt):
					$adress = $clt->adresseClient;
					$telc = $clt->telClient;
					$regimec = $clt->regimeClient;
					$rccmc = $clt->rccmClient;
					$ifuc = $clt->ifuClient;
					$divisionc = $clt->divisionClient;
				endforeach;
			?>
			
		
				<P><?= "Facture N° ".$code;?></P>
				
			</span>
			
			<div align="left">
			<span style="font-size:8px; " >
			<table  border="1" width="30">
				<thead>
					<tr>
						<th style="text-align:center">#</th>
						<th style="text-align:center">Désignation</th>
						<th style="text-align:center">Qte</th>
						<th style="text-align:center">PVU</th>
						<th style="text-align:center">PVT</th>
					</tr>
				</thead>
				<?php
					$nl = 1;
					$sum = 0;
					foreach($arts as $art):
				?>
				<tr>
					<td style="width:5px"><?=$nl++;?></td>
					<td style="width:65px;text-align:left"><?=getLibArt($art->facture_codeArticle);?></td>
					<td style="width:15px;text-align:center"><?=number_format($art->quantiteAFacture, 0, ',', ' ');?></td>
					<td style="width:25px;text-align:right"><?=number_format($art->prixVenteFacture, 0, ',', ' ');?></td>
					<td style="width:15px;text-align:right"><?=number_format($art->totalFacture, 0, ',', ' ');?></td>
				</tr>
				<?php
					$sum += $art->totalFacture;
					endforeach;
					$taux = ($remise*100)/$sum;
				?>
				<tr>
					<td colspan="4" style="text-align:left">TOTAL HT</td>
					<td style="text-align:right"><b><?=number_format($sum, 0, ',', ' ');?></b></td>
				</tr>
				<tr>
					<td colspan="4" style="text-align:left">REMISE&nbsp;&nbsp;&nbsp;<b><?=$taux."%";?></b></td>
					<td style="text-align:right"><b><?=number_format($remise, 0, ',', ' ');?></b></td>
				</tr>
				<tr>
					<td colspan="4" style="text-align:left">TVA&nbsp;&nbsp;&nbsp;<b><?=(getcoefTVA()*100)."%";?></b></td>
					<td style="text-align:right"><b><?=number_format(getTVA($code), 0, ',', ' ');?></b></td>
				</tr>
				<tr>
					<td colspan="4" style="text-align:left">NET A PAYER</td>
					<td style="text-align:right"><b><?=number_format(getTTC($code), 0, ',', ' ');?></b></td>
				</tr>
			</table>
			<p> <?=$nom.''?> vous remercie!</p>
			</span>
			</div>
			
						
		</div><!-- ./content -->
	</body>
</html>
<?php

    $cont = ob_get_clean();
	ob_end_clean();
    // convert to PDF
    require_once('php/html2pdf/html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF('P', 'A8', 'fr');
        $html2pdf->pdf->SetDisplayMode('fullpage');
     // $html2pdf->pdf->SetProtection(array('print'), 'spipu');
        $html2pdf->writeHTML($cont);
        $html2pdf->Output('Facture.pdf');
    }
    catch(HTML2PDF_exception $e)
	{
        echo $e;
        exit;
    }