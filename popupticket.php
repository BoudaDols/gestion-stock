<?php
require('\php\fpdf\fpdf.php');
//require('\php\fpdf\mc_table.php');
require_once('php/session.php');
require_once('php/fonction.php');
require_once('php/ChiffresEnLettres.php');
$lettre = new ChiffreEnLettre();

$logo = getLogo();
$nom = getNom();
$adr = getAdr();
$tel = getTel1()." | ".getTel2();
$bank = getBank();
$rccm = getRCCM();
$ifu= getIFU();


//taille fixe du ticket de caisse
$fixer=100;

//reference de la facture
$code=$_GET["ref"];


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
$nbligneRequete = count($arts);
$nbligne = $fixer + ($nbligneRequete * 10);

//$pdf=new PDF_MC_Table();
$pdf = new FPDF('P','mm',array(80,$nbligne));
$pdf->SetMargins(0,1.5,0);
$pdf->AddPage();
//tableau facture
//$pdf->SetWidths(array(3,7,32,7,12,15));

//entete facture
$pdf->SetFont('Arial','B',15);
$pdf->Image($logo,27.5,7,25,25);
$pdf->Ln(30);
//$pdf->Cell(80,6,'Cave TENE',0,1,'C');
$pdf->SetFont('Arial','',8);
$pdf->Cell(80,5,$adr,0,1,'C');
$pdf->Cell(80,5,$tel,0,1,'C');
//$pdf->Cell(80,5,'Vendeur : Joel KINI',0,1,'C');
$pdf->SetFont('Arial','B',8);
$pdf->Cell(80,5,'Facture No : '.$code.' | '.date('d-m-y H:i:s').'',0,1,'C');
//Fin entete

//debut corps
//#1
$pdf->Ln(1);
$pdf->SetFont('Arial','B',8);
$pdf->Cell(3,5,'','C');
$pdf->Cell(7,5,'No',0,'C');
$pdf->Cell(32,5,'Designation',0,'C');
$pdf->Cell(7,5,'Qte',0,'C');
$pdf->Cell(12,5,'PU',0,'C');
$pdf->Cell(15,5,'PT',0,1);


//calcul
$nl = 1;
$sum = 0;
foreach($arts as $art){
	//affichage des infos
	$pdf->Ln(1);
	$pdf->SetFont('Arial','B',8);
	$pdf->Cell(3,5,'','C');
	$pdf->Cell(7,5,$nl,0,'C');
	//$pdf->MultiCell(30,5,getLibArt($art->facture_codeArticle),0,1);

	//Tronquer les designations
	$chaine = getLibArt($art->facture_codeArticle);
	$lg_max = 17; //nombre de caractère autoriser

	if (strlen($chaine) > $lg_max)
	{
	$chaine = substr($chaine, 0, $lg_max);
	//$last_space = strrpos($chaine, " ");
	//$chaine = substr($chaine, 0, $last_space).".";
	}

	$pdf->Cell(32,5,$chaine,0,'C');
	//$pdf->Cell(32,5,getLibArt($art->facture_codeArticle),0,'C');
	$pdf->Cell(7,5,number_format($art->quantiteAFacture, 0, ',', ' '),0,'C');
	$pdf->Cell(12,5,number_format($art->prixVenteFacture, 0, ',', ' '),0,'C');
	$pdf->Cell(15,5,number_format($art->totalFacture, 0, ',', ' '),0,1);

	$sum += $art->totalFacture;
	$nl+=1;
}
//$taux = ($remise*100)/$sum;

$tva = getTVA($code);
//calcul tva
if($tva == 0){
	$mtttva = 0;
	$mtt = $sum + $mtttva - $remise;
}else{
	$mtttva = ($sum*0.18);
	$mtt = $sum + $mtttva - $remise;
}

//Remise
$pdf->Ln(1);
$pdf->SetFont('Arial','',8);
$pdf->Cell(80,5,'Remise : '.number_format($remise, 0, ',', ' ').' FCFA  |  TVA  : '.number_format($mtttva, 0, ',', ' ').' FCFA',0,1,'C');

//#Total HT
$pdf->Ln(1);
$pdf->SetFont('Arial','',8);
$pdf->Cell(80,5,'Total HT  : '.number_format($sum, 0, ',', ' ').' FCFA',0,1,'C');

//#Total TTC
$pdf->Ln(1);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(80,5,'Total TTC  : '.number_format($mtt, 0, ',', ' ').' FCFA',0,1,'C');

//fin corps

//pieds de page
$pdf->SetFont('Arial','',8);
//$pdf->Ln();
$pdf->Ln();
$pdf->SetFont('Arial','I',7);
$pdf->Cell(0,0,'La cave TENE vous remercie pour votre achat. A bientot !!!!',0,1,'C');
//fin pieds de page
$pdf->Output();
?>