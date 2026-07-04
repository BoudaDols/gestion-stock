<?php
	// session_start();
	require_once('php/fonction.php');
	$bdd = new DB();
	
	$pagetitle = "GSF | Codes et Etat du stock";
	$pagestitle = " Codes et Etat du stock"; // A remplacer après
	$bcrumb = "Statistique >  Codes et Etat du stock";
	
	ob_start();
?>
	
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-bars"></i>
				<h3 class="box-title">Liste des codes</h3>
			</div>
			
			<div class="box-body">
				<div class="row">
					<div class="col-lg-3 col-xs-6">
						<div class="small-box bg-aqua">
							<div class="inner">
								<h3>
									<?=Count(SQLSelect("SELECT * FROM article WHERE statutArticle='ON'"));?>
								</h3>
								<p>ARTICLES</p>
							</div>
							<div class="icon">
								<i class="fa fa-spinner"></i>
							</div>
							<button onclick="javascript:popup_art();" class="btn btn-default">
								Afficher la liste <i class="fa fa-eye"></i>
							</button>
						</div>
					</div>
					
					<div class="col-lg-3 col-xs-6">
						<div class="small-box bg-green">
							<div class="inner">
								<h3>
									<?=Count(SQLSelect("SELECT * FROM typearticle WHERE statutTypeA='ON'"));?>
								</h3>
								<p> CATEGORIES D'ARTICLES</p>
							</div>
							<div class="icon">
								<i class="fa fa-gavel"></i>
							</div>
							<button onclick="javascript:popup_cart();" class="btn btn-default">
								Afficher la liste <i class="fa fa-eye"></i>
							</button>
						</div>
					</div>
					
					<div class="col-lg-3 col-xs-6">
						<div class="small-box bg-yellow">
							<div class="inner">
								<h3>
									<?=Count(SQLSelect("SELECT * FROM client WHERE statutClient='ON'"));?>
								</h3>
								<p>CLIENTS</p>
							</div>
							<div class="icon">
								<i class="fa fa-users"></i>
							</div>
							<button onclick="javascript:popup_clt();" class="btn btn-default">
								Afficher la liste <i class="fa fa-eye"></i>
							</button>
						</div>
					</div>
				
					<div class="col-lg-3 col-xs-6">
						<div class="small-box bg-red">
							<div class="inner">
								<h3>
									<?=Count(SQLSelect("SELECT * FROM fournisseur WHERE statutFournisseur='ON'"));?>
								</h3>
								<p>FOURNISSEURS</p>
							</div>
							<div class="icon">
								<i class="fa fa-truck"></i>
							</div>
							<button onclick="javascript:popup_frs();" class="btn btn-default">
								Afficher la liste <i class="fa fa-eye"></i>
							</button>
						</div>
					</div>
				
				</div>
				<div class="row">
					<div class="col-lg-3 col-xs-6">
						<div class="small-box bg-purple">
							<div class="inner">
								<h3>
									<?=Count(SQLSelect("SELECT * FROM article WHERE statutArticle='ON'"));?>
								</h3>
								<p>STOCK ARTICLES</p>
							</div>
							<div class="icon">
								<i class="fa fa-spinner"></i>
							</div>
							<button onclick="javascript:popup_stock();" class="btn btn-default">
								Afficher la liste <i class="fa fa-eye"></i>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
<?php
	$content = ob_get_clean();
	require_once('patterning.php');
?>

	<script type="text/javascript">
		
		function popup_art()
		{
			width = 1200;
			height = 800;
			if(window.innerWidth)
			{
				var left = (window.innerWidth-width)/2;
				var top = (window.innerHeight-height)/2;
			}
			else
			{
				var left = (document.body.clientWidth-width)/2;
				var top = (document.body.clientHeight-height)/2;
			}
			window.open('popup_art.php','GSF | Vente à valider','menubar=no, scrollbars=no, top='+top+', left='+left+', width='+width+', height='+height+'');
		}
		
		function popup_cart()
		{
			width = 1200;
			height = 800;
			if(window.innerWidth)
			{
				var left = (window.innerWidth-width)/2;
				var top = (window.innerHeight-height)/2;
			}
			else
			{
				var left = (document.body.clientWidth-width)/2;
				var top = (document.body.clientHeight-height)/2;
			}
			window.open('popup_cart.php','GSF | Vente à valider','menubar=no, scrollbars=no, top='+top+', left='+left+', width='+width+', height='+height+'');
		}
		
		function popup_clt()
		{
			width = 1200;
			height = 800;
			if(window.innerWidth)
			{
				var left = (window.innerWidth-width)/2;
				var top = (window.innerHeight-height)/2;
			}
			else
			{
				var left = (document.body.clientWidth-width)/2;
				var top = (document.body.clientHeight-height)/2;
			}
			window.open('popup_clt.php','GSF | Vente à valider','menubar=no, scrollbars=no, top='+top+', left='+left+', width='+width+', height='+height+'');
		}
		
		function popup_frs()
		{
			width = 1200;
			height = 800;
			if(window.innerWidth)
			{
				var left = (window.innerWidth-width)/2;
				var top = (window.innerHeight-height)/2;
			}
			else
			{
				var left = (document.body.clientWidth-width)/2;
				var top = (document.body.clientHeight-height)/2;
			}
			window.open('popup_frs.php','GSF | Vente à valider','menubar=no, scrollbars=no, top='+top+', left='+left+', width='+width+', height='+height+'');
		}
		
		function popup_stock()
		{
			width = 1200;
			height = 800;
			if(window.innerWidth)
			{
				var left = (window.innerWidth-width)/2;
				var top = (window.innerHeight-height)/2;
			}
			else
			{
				var left = (document.body.clientWidth-width)/2;
				var top = (document.body.clientHeight-height)/2;
			}
			window.open('popup_stock.php','GSF | Articles en stock','menubar=no, scrollbars=no, top='+top+', left='+left+', width='+width+', height='+height+'');
		}
	</script>