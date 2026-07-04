<?php
require_once('php/session.php');
require_once('php/fonction.php');

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$pagetitle = "GSF | Tableau de bord";
$pagestitle = "Tableau de bord";
$bcrumb = "";

$msg = "";
$classmsg = "";
$button = "";
ob_start();
?>
<script src="/php/Highcharts/code/highcharts.js"></script>
<script src="/php/Highcharts/code/highcharts-3d.js"></script>
<script src="/php/Highcharts/code/modules/exporting.js"></script>
<script src="/php/Highcharts/code/modules/export-data.js"></script>


<div class="row">
		<section class="col-lg-12 connectSortable">
		<div class="box box-primary">
				
			<div class="container">
				 <div class="row">
				  	<div class="col-sm-1">
				    </div>
				    <div class="col-sm-4">
				      <h3 style="text-align: center;">Vente du jour</h3>
				       <div id="venteJour" style="height: 250px"></div>
				    </div>
				     <div class="col-sm-4">
				      <h3 style="text-align: center;">Petite caisse</h3>
				       <div id="petiteCaisse" style="height: 300px"></div>
				     </div>
				    <div class="col-sm-2"> 
				    </div>
				</div>
			</div>
		</div>
		</section>
</div>
<?php 
	//vente comptant du jour
	$actu = date('Y-m-d');
	$tventecomptant = 0;
	$sqlvcp = "SELECT montantReglement FROM reglement WHERE reglement_codeFacture != '' AND 
	statutReglement = 'C' AND dateReglement = :dateJour";
	$ventecpjr = SQLSelect($sqlvcp, [':dateJour' => $actu]);
	if ($ventecpjr) {
		foreach ($ventecpjr as $vte) {
			$tventecomptant += $vte->montantReglement;
		}
	}

	//vente credit du jour
	$tventecredit = 0;
	$sqlvcd = "SELECT montantReglement FROM reglement WHERE reglement_codeFacture != '' AND 
	statutReglement = 'A' AND dateReglement = :dateJour";
	$ventecdjr = SQLSelect($sqlvcd, [':dateJour' => $actu]);
	if ($ventecdjr) {
		foreach ($ventecdjr as $vte) {
			$tventecredit += $vte->montantReglement;
		}
	}

	//calcul pourcentage
	$total = $tventecomptant + $tventecredit;
	if ($total == 0) {
		$pourcentagecomptant = 50.0;
		$pourcentagecredit = 50.0;
	} else {
		$pourcentagecomptant = ($tventecomptant * 100) / $total;
		$pourcentagecredit = ($tventecredit * 100) / $total;
	}

    //operations caisse du jour — entrees
    $tentre = 0;
    $sqle = "SELECT montantReglement FROM reglement WHERE reglement_codeFacture = '' AND 
    statutReglement = 'E' AND dateReglement = :dateJour";
    $ejr = SQLSelect($sqle, [':dateJour' => $actu]);
    if ($ejr) {
        foreach ($ejr as $e) {
            $tentre += $e->montantReglement;
        }
    }

    //sorties
    $tsortie = 0;
    $sqls = "SELECT montantReglement FROM reglement WHERE reglement_codeFacture = '' AND 
    statutReglement = 'D' AND dateReglement = :dateJour";
    $sjr = SQLSelect($sqls, [':dateJour' => $actu]);
    if ($sjr) {
        foreach ($sjr as $s) {
            $tsortie += $s->montantReglement;
        }
    }

    //calcul pourcentage
    $totalc = $tentre + $tsortie;
    if ($totalc == 0) {
        $pourcentageentre = 50.0;
        $pourcentagesortie = 50.0;
    } else {
        $pourcentageentre = ($tentre * 100) / $totalc;
        $pourcentagesortie = ($tsortie * 100) / $totalc;
    }

?>
<script type="text/javascript">
            Highcharts.chart('venteJour', {
                chart: {
                    type: 'pie',
                    options3d: {
                        enabled: true,
                        alpha: 45,
                        beta: 0
                    }
                },
                title: {
                    text: ''
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        depth: 35,
                        dataLabels: {
                            enabled: true,
                            format: '{point.name}'
                        }
                    }
                },
                series: [{
                    type: 'pie',
                    name: 'Occupation',
                    data: [
                        ['Comptant', <?php echo $pourcentagecomptant ?>],
                        ['Crédit', <?php echo $pourcentagecredit ?>]
                    ]
                }]
            });
     Highcharts.chart('petiteCaisse', {
                chart: {
                    type: 'pie',
                    options3d: {
                        enabled: true,
                        alpha: 45,
                        beta: 0
                    }
                },
                title: {
                    text: ''
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        depth: 35,
                        dataLabels: {
                            enabled: true,
                            format: '{point.name}'
                        }
                    }
                },
                series: [{
                    type: 'pie',
                    name: 'Occupation',
                    data: [
                        ['Entrées', <?php echo $pourcentageentre ?>],
                        ['Sorties',  <?php echo $pourcentagesortie ?>]
                    ]
                }]
            });
		</script>

<?php
	$content = ob_get_clean();
	require_once('patterning.php');
?>