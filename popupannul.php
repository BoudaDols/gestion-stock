<?php
	require_once('php/session.php');
	require_once('php/fonction.php');
	
	$pagetitle = "GSF | Ventes annulées";
	$pagestitle = "Détails des Factures : Ventes annulées"; // A remplacer après
	$bcrumb = "Vente > Ventes annulées";
	
	if(isset($_GET['codefact']))
	{
		$code = $_GET['codefact'];
	}
	
	ob_start();
?>

	<?php
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
		$arts = SQLSelect("SELECT * FROM facture WHERE codeFacture = :code", [':code' => $code]);
	?>
	<div id="toprint">	
		<section class="invoice">
			<div class="row">
				<div class="col-xs-12">
				  <h2 class="page-header">
					VENTE <?=$type;?> - Facture N° <?=$code;?>
					<small class="pull-right"><b>Date: </b><?=date_format(date_create($date),'d/m/Y');?></small>
				  </h2>
				</div>
			</div>
			<div class="row invoice-info">
				<div class="col-sm-6 invoice-col">
					<br>
					<address>
						<strong>Event'24 SARL</strong><br>
						17 BP 254 OUAGA 17<br>
						(+2226) 76424845<br>
						htpps://www.event24apps.com<br>
						Email: info@event24apps.com
					</address>
				</div>
				<div class="col-sm-6 invoice-col">
					<b>A</b>
					<address>
						<?php
							$clts = SQLSelect("SELECT * FROM client WHERE codeClient = :client", [':client' => $client]);
							foreach($clts as $clt):
								$adress = $clt->adresseClient;
								$tel = $clt->telClient;
							endforeach;
						?>
						<strong><?=$client;?> : <?=getClient($client);?></strong><br>
						<?=$adress;?><br>
						Tel: <?=$tel;?><br/>
					</address>
				</div>
			</div>
			
			<div class="row">
				<div class="col-xs-12 table-responsive">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>#</th>
								<th>Désignation</th>
								<th>Quantité</th>
								<th>Prix Unitaire</th>
								<th>Prix Total</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$nl = 1;
								$sum = 0;
								foreach($arts as $art):
							?>
									<tr>
										<td><?=$nl++;?></td>
										<td><?=getLibArt($art->facture_codeArticle);?></td>
										<td><?=number_format($art->quantiteAFacture, 0, ',', ' ');?></td>
										<td><?=number_format($art->prixVenteFacture, 0, ',', ' ');?></td>
										<td><?=number_format($art->totalFacture, 0, ',', ' ');?></td>
									</tr>
							<?php
								$sum += $art->totalFacture;
								endforeach;
							?>
							<tr>
								<td colspan="4">TOTAL</td>
								<td><b><?=number_format($sum, 0, ',', ' ');?></b></td>
							</tr>
							<tr>
								<td colspan="4">REMISE</td>
								<td><b><?=number_format($remise, 0, ',', ' ');?></b></td>
							</tr>
							<tr>
								<td colspan="4">NET A PAYER</td>
								<td><b><?=number_format($sum-$remise, 0, ',', ' ');?></b></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</section>
	</div>
<?php
	$content = ob_get_clean();
	require_once('patterning.php');
?>