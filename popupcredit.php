<?php
	require_once('php/session.php');
	require_once('php/fonction.php');
	require_once('php/ChiffresEnLettres.php');
	$lettre = new ChiffreEnLettre();
	
	$pagetitle = "GSF | Factures Crédits";
	$pagestitle = "Impression des Réglements Factures crédits"; // A remplacer après
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
		$facts = SQLSelect("SELECT DISTINCT facture_codeClient, remiseFacture, dateFacture, facture_codeTypeF 
		FROM facture WHERE codeFacture = :code", [':code' => $code]);
		if(!empty($facts))
		{
			foreach($facts as $fact):
				$client = $fact->facture_codeClient;	
				$date = $fact->dateFacture;
				$remise = $fact->remiseFacture;
				$type = $fact->facture_codeTypeF;
			endforeach;
		}
		$numVResult = SQLSelect("SELECT * FROM reglement WHERE reglement_codeFacture = :code", [':code' => $code]);
		$numV = is_array($numVResult) ? count($numVResult) : 0;
	}
				$clts = SQLSelect("SELECT * FROM client WHERE codeClient = :client", [':client' => $client]);
				foreach($clts as $clt):
					$adress = $clt->adresseClient;
					$telc = $clt->telClient;
					$regimec = $clt->regimeClient;
					$rccmc = $clt->rccmClient;
					$ifuc = $clt->ifuClient;
					$divisionc = $clt->divisionClient;
				endforeach;
	require_once('php/fonction.php');
    //Infos societe
    $logo = getLogo();
    $nom = getNom();
    $adr = getAdr();
    $tel = getTel1()."  /  ".getTel2();
    $bank = getBank();
    $rccm = getRCCM();
    $ifu= getIFU();
    
    if(isset($_GET['codefact']))
    {
        $code = $_GET['codefact'];

        //infos facture
        $facts = SQLSelect("SELECT DISTINCT facture_codeClient, remiseFacture, dateFacture, 
        facture_codeTypeF, tvaFacture, cmdFacture
        FROM facture WHERE codeFacture = :code", [':code' => $code]);
        if(!empty($facts))
        {
            foreach($facts as $fact):
                $client = $fact->facture_codeClient;    
                $date = $fact->dateFacture;
                $remise = $fact->remiseFacture;
                $type = $fact->facture_codeTypeF;
                $tva = $fact->tvaFacture;
                $nocmd = $fact->cmdFacture;
                $pu =  $fact->totalFacture;
                $totalHT = $totalHT + $pu;
            endforeach;
        }

        //infos clients
        $clients = SQLSelect("SELECT *
        FROM client WHERE codeClient = :client", [':client' => $client]);
        if(!empty($clients))
        {
            foreach($clients as $cl):
                $nomClt = $cl->nomClient;    
            endforeach;
        }

        //infos prix total
        $totalHT=0;
        $prix = SQLSelect("SELECT totalFacture 
        FROM facture WHERE codeFacture = :code", [':code' => $code]);
        if(!empty($prix))
        {
            foreach($prix as $pr):
                $pu =  $pr->totalFacture;
                $totalHT = $totalHT + $pu;
            endforeach;
        }

        //calcul tva
        $tvaReelle=0;
        if ($tva==1) {
            $tvaReelle= ($totalHT*18)/100;
        }else{
            $tvaReelle = 0;
        }

        //calcul prix total
        $totalTTC = $totalHT - $remise + $tvaReelle;
    }

    ob_start(); 
?>

<style type="text/css">
    table{width:100%; border-collapse: collapse;}
    hr{height:"5px";}
    hr,h1{color:#7d002e;}
    .classDoit{width:25%;border:bloc;padding-top:10px;padding-bottom:10px;padding-left:10px;padding-right:10px;}
    .bstyle{border: 1px solid black;padding-top:5px;padding-bottom:5px;padding-left:5px;padding-right:5px;}
</style>
	<page backtop="15mm" backleft="10mm" backright="10mm">
         <!--Pieds de page-->
        <page_footer style="text-align:center;">
            <hr>
            <?php echo "$nom"; ?> - <?php echo "$adr"; ?> <br>
            <?php echo "$rccm"; ?> - IFU  <?php echo "$ifu"; ?><br>
            <?php //echo "$bank"; ?> E-mail : contact@cave.tene.com  - Tel.: <?php echo "$tel"; ?>
        </page_footer>
        <!--INFOS Société-->
        <table style="">
            <tr>
                <td style="width:60%;text-align:left;">
                    <img src="<?=$logo;?>" alt="Logo-TECH 24" height="150" width="150" />
                </td>
            </tr>
            </table><br/><br/><br/><br/>
        <!--Titre BL-->
        <table style="">
            <tr>
                <td style="width:100%;text-align:center;background:#7d002e;">
                    <h3 style="color:#ffffff;">FACTURE A CREDIT No: <?php echo "$code"; ?></h3>
                </td>
            </tr>
        </table><br/><br/>
        <!--INFOS Client-->
         <table style="">
            <tr>
                <td class="classDoit">
                    <b>Client:</b><br/>
                     <?php echo "$nomClt"; ?>
                </td>
                <td class="classDoit">
                    <b>Objet:</b><br/>
                    Achat de boissons
                </td>
                <td class="classDoit">
                    <b>Date:</b><br/>
                   <?php echo "$date"; ?>
                </td>
                <td class="classDoit">
                    <b>Commande No:</b><br/>
                   <?php echo "$nocmd"; ?>
                </td>
            </tr>
        </table>
         <!--INFOS Client-->
         <table style="">
            <tr>
                <td class="classDoit">
                    <b>Versement No: <?php echo "$numV"; ?></b><br/>
                     
                </td>
                <td class="classDoit">
                    <b>Reste à payer:</b><br/>
                    <?php echo number_format((getTTC($code)-getSumPaidC($code)+getLastVers($code)), 0, ',', ' ')?> FCFA
                </td>
                <td class="classDoit">
                    <b>Montant versé:</b><br/>
                   <?php echo number_format(getLastVers($code), 0, ',', ' '); ?> FCFA
                </td>
                <td class="classDoit">
                    <b>Nouveau solde:</b><br/>
                    <?php echo number_format((getTTC($code)-getSumPaidC($code)), 0, ',', ' ')?> FCFA
                </td>
            </tr>
        </table><br/><br/>
        <!--Designation livraison-->
         <table style="text-align:center;">
            <thead >
                <tr style="background-color:#7d002e; color:#ffffff;">
                    <td style="width:10%" class="bstyle">
                        <b>No:</b><br/>
                    </td>
                    <td style="width:40%;" class="bstyle">
                        <b>Désignation:</b><br/>
                    </td>
                    <td style="width:10%"class="bstyle">
                        <b>Quantité:</b><br/>
                    </td>
                    <td style="width:20%"class="bstyle">
                        <b>Prix Unitaire:</b><br/>
                    </td>
                    <td style="width:20%"class="bstyle">
                        <b>Prix Total</b><br/>
                    </td> 
                </tr>  
            </thead><br/><br/><br/><br/><br/><br/>
            <tbody>
<?php
//infos articles
$i = 1;
$arts = SQLSelect("SELECT DISTINCT facture_codeArticle, quantiteAFacture, prixVenteFacture, totalFacture
FROM facture WHERE codeFacture = :code", [':code' => $code]);
if(!empty($arts))
{
    foreach($arts as $art):
        $cdArt = $art->facture_codeArticle;
        $qte = $art->quantiteAFacture;
        $pu = $art->prixVenteFacture;
        $pt = $art->totalFacture;
        //obtenir designation article
        $designs = SQLSelect("SELECT designationArticle
        FROM article WHERE codeArticle = :cdArt", [':cdArt' => $cdArt]);
        if(!empty($designs))
        {
            foreach($designs as $design):
                $nomArt = $design->designationArticle;                
            endforeach;
        }
?>
                <tr>
                     <td style="width:10%;" class="bstyle">
                        <?php echo "$i"; ?>
                    </td>
                    <td style="width:40%;" class="bstyle">
                        <?php echo "$nomArt"; ?>
                    </td>
                    <td style="width:10%"class="bstyle">
                        <?php echo "$qte"; ?>
                    </td>
                    <td style="width:20%"class="bstyle">
                        <?php echo "$pu"; ?>
                    </td>
                    <td style="width:20%"class="bstyle">
                       <?php echo "$pt"; ?>
                    </td> 
                </tr>  

<?php
    $i++;
    endforeach;
}

?>
               
                
                <!--Totaux-->
                <tr style="background-color:#dc6893; color:#ffffff;">
                    <td colspan="4" class="bstyle">
                        <b>Total HT</b>
                    </td>
                    <td style="width:20%" class="bstyle">
                       <?php echo "$totalHT"; ?>
                    </td> 
                </tr>  
                 <tr style="background-color:#dc6893; color:#ffffff;">
                    <td colspan="4" class="bstyle">
                        <b>Remise</b>
                    </td>
                    <td style="width:20%" class="bstyle">                    
                        <?php echo "$remise"; ?>
                    </td> 
                </tr>  
                <tr style="background-color:#dc6893; color:#ffffff;">
                    <td colspan="4" class="bstyle">
                        <b>TVA</b>
                    </td>
                    <td style="width:20%" class="bstyle">
                       <?php echo "$tvaReelle"; ?>
                    </td> 
                </tr> 
                <tr style="background-color:#7d002e; color:#ffffff;">
                    <td colspan="4" class="bstyle">
                        <b>Total TTC</b>
                    </td>
                    <td style="width:20%" class="bstyle">
                      <?php echo "$totalTTC"; ?>
                    </td> 
                </tr>   
            </tbody>
        </table><br/><br/>
        <!--Visa livreur receptionniste-->
         <table style="">
            <tr>
                <td style="width:50%;text-align: center;">
                   
                </td>
                <td style="width:50%;text-align: center;">
                    <b>Le responsable :</b><br/>
                </td>
            </tr>
        </table><br/><br/>
    </page>
<?php
    $content = ob_get_clean();
    // convert to PDF
    require('php/html2pdf/html2pdf.class.php');
    try
    {
        $pdf = new HTML2PDF('P','A4','fr');
        $pdf->writeHTML($content);
        ob_end_clean();
        $pdf->Output('BordLivr.pdf');
    }
    catch(HTML2PDF_exception $e)
	{
        echo $e;
        exit;
    }
?>
	