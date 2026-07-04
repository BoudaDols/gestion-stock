<?php
require_once('php/session.php');
require_once('php/fonction.php');

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
?>   
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title><?php if(isset($pagetitle)) echo $pagetitle; else echo "ACCUEIL | GSF"; ?></title>
		<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
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

		<!-- Highcharts -->
		<script src="https://code.highcharts.com/highcharts.js"></script>
		<script src="https://code.highcharts.com/highcharts-3d.js"></script>
		<script src="https://code.highcharts.com/modules/exporting.js"></script>
		
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
		<![endif]-->
		
	</head>
	<body class="skin-blue layout-boxed sidebar-min">

		<div class="wrapper">
		  
			<header class="main-header">
				<!-- Logo -->
				<a href="accueil.php" class="logo">
					<!-- mini logo for sidebar mini 50x50 pixels -->
					<span class="logo-mini"><b>S</b>&F</span>
					<!-- logo for regular state and mobile devices -->
					<span class="logo-lg"><b>Stock & </b>Facturation</span>
				</a>
				<!-- Header Navbar: style can be found in header.less -->
				<nav class="navbar navbar-static-top" role="navigation">
					<!-- Sidebar toggle button-->
					<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
						<span class="sr-only">Toggle navigation</span>
					</a>
					<div class="navbar-custom-menu">
						<ul class="nav navbar-nav">
							<!-- User Account: style can be found in dropdown.less -->
							<li class="dropdown user user-menu">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<img src="dist/img/avatar.png" class="user-image" alt="User Image"/>
									<span class="hidden-xs"><?php $tcpt = $_SESSION['compte'];echo $tcpt;?></span>
								</a>
								<ul class="dropdown-menu">
									<!-- User image -->
									<li class="user-header">
										<img src="dist/img/avatar.png" class="img-circle" alt="User Image" />
										<p>
											<?=$_SESSION['nom'];?>
										</p>
									</li>
									<!-- Menu Footer-->
									<li class="user-footer">
										<div class="pull-left">
											<a href="moncompte.php" class="btn btn-default btn-flat">Mon Profil</a>
										</div>
										<div class="pull-right">
											<a href="deconnexion.php" class="btn btn-default btn-flat">Me déconnecter</a>
										</div>
									</li>
								</ul>
							</li>
							<!-- Control Sidebar Toggle Button -->
							<li>
								<a href="#" data-toggle="control-sidebar"><i class="fa fa-outdent"></i></a>
							</li>
						</ul>
					</div>
				</nav>
			</header>
			<!-- Left side column. contains the logo and sidebar -->
			<aside class="main-sidebar">
				<!-- sidebar: style can be found in sidebar.less -->
				<section class="sidebar">
				  <!-- Sidebar user panel -->
					<div class="user-panel">
						<div class="pull-left image">
							<img src="dist/img/avatar.png" class="img-circle" alt="User Image" />
						</div>
						<div class="pull-left info">
							<p><?=$tcpt;?></p>
							<i class="fa fa-circle text-success"></i> En ligne
						</div><!--<img src="dist/img/gsf_logo.jpg">-->
					</div>
					<!-- sidebar menu: : style can be found in sidebar.less -->
					<b class="info">MENU GENERAL</b>
					<?php
						$sqlmenu = "SELECT DISTINCT idMenu, titreMenu, iconeMenu FROM menu, 
						menuitem, access WHERE idMenu=menu_idMenu AND 
						codeSousMenu=access_codeSousMenu AND access_codeCompte = :compte 
						ORDER BY idMenu ASC";
						$menus = SQLSelect($sqlmenu, [':compte' => $tcpt]);
						if ($menus): foreach($menus as $menu):
					?>		
						<ul class="sidebar-menu">
							<li class="treeview">
								<a href="#">
									<i class="<?=htmlspecialchars($menu->iconeMenu)?>"></i>
									<span><?=htmlspecialchars($menu->titreMenu)?></span>
									<i class="fa fa-angle-left pull-right"></i>
								</a>
								<?php
									$idmenu = $menu->idMenu;
									$sqlitem = "SELECT * FROM menuitem, access WHERE codeSousMenu=access_codeSousMenu 
									AND menu_idMenu = :idmenu AND access_codeCompte = :compte";
									$items = SQLSelect($sqlitem, [':idmenu' => $idmenu, ':compte' => $tcpt]);
									if ($items): foreach($items as $item):
								?>
										<ul class="treeview-menu">
											<li>
												<a href="<?= htmlspecialchars($item->lienSousMenu);?>">
													<i class="glyphicon glyphicon-chevron-right"></i><?= htmlspecialchars($item->titreSousMenu);?>
												</a>
											</li>
										</ul>
									<?php
										endforeach; endif;
									?>
							</li>
						</ul>
					<?php						
						endforeach; endif;
					?>
				</section>
				<!-- /.sidebar -->
			</aside>

			<!-- Content Wrapper. Contains page content -->
			<div class="content-wrapper">
				<!-- Content Header (Page header) -->
				<section class="content-header">
					<h1>
						<?php if(isset($pagetitle)) echo $pagestitle; else echo "GESTION DE STOCK & SUIVI DE LA FACTURATION"; ?>
					</h1>
					<ol class="breadcrumb">
						<li><!--<a href="#">--><i class="fa fa-dashboard"></i> Accueil<!--</a>--></li>
						<?php if(isset($bcrumb)):?>
							<li class="active"><?= $bcrumb; ?></li>
						<?php endif; ?>
					</ol>
				</section>

				<!-- Main content -->
				<section class="content">
					<?php if(isset($content)) echo $content; ?>
				</section><!-- /.content -->
				
			</div><!-- /.content-wrapper -->

			<footer class="main-footer">
				<div class="pull-right hidden-xs">
					<b>Version</b> 2.0
				</div>
				<strong>
					Copyright &copy; 2017
					<a href="http://event24apps.com">Event'24</a>
				</strong> 
				All rights reserved.
			</footer>
		</div><!-- ./wrapper -->

		<!-- jQuery 2.1.3 -->
		<script src="plugins/jQuery/jQuery-2.1.3.min.js"></script>
		<!-- jQuery UI 1.11.2 -->
		<script src="http://code.jquery.com/ui/1.11.2/jquery-ui.min.js" type="text/javascript"></script>
		<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
		<script>
		  $.widget.bridge('uibutton', $.ui.button);
		</script>
		<!-- Bootstrap 3.3.2 JS -->
		<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>    
		<!-- Morris.js charts -->
		<script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
		<script src="plugins/morris/morris.min.js" type="text/javascript"></script>
		<!-- Sparkline -->
		<script src="plugins/sparkline/jquery.sparkline.min.js" type="text/javascript"></script>
		<!-- jvectormap -->
		<script src="plugins/jvectormap/jquery-jvectormap-1.2.2.min.js" type="text/javascript"></script>
		<script src="plugins/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>
		<!-- jQuery Knob Chart -->
		<script src="plugins/knob/jquery.knob.js" type="text/javascript"></script>
		<!-- daterangepicker -->
		<script src="plugins/daterangepicker/moment.min.js" type="text/javascript"></script>
		<script src="plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
		<!-- datepicker -->
		<script src="plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
		<!-- Bootstrap WYSIHTML5 -->
		<script src="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
		<!-- Slimscroll -->
		<script src="plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
		<!-- FastClick -->
		<script src='plugins/fastclick/fastclick.min.js'></script>
		<!-- AdminLTE App -->
		<script src="dist/js/app.min.js" type="text/javascript"></script>    
		<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
		<script src="dist/js/pages/dashboard.js" type="text/javascript"></script>    
		<!-- AdminLTE for demo purposes -->
		<script src="dist/js/demo.js" type="text/javascript"></script>
		<!-- jquery chained list -->
		<script src="dist/js/jquery.chained.js" type="text/javascript"></script>
	</body>
</html>