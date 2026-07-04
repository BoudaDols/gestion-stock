<?php
require_once('php/session.php');
require_once('php/fonction.php');

$style = "style='display:none'";
$class = "";
$msg = "";

if (isset($_POST['connect'])) {
    $login = trim($_POST['login']);

    $sql = "SELECT * FROM user WHERE loginUser = :login AND statutCompteUser = 1";
    $repons = SQLSelect($sql, [':login' => $login]);

    if (!$repons || !password_verify($_POST['pwd'], $repons[0]->mdpUser)) {
        $msg = "Login et/ou Mot de passe incorrects!";
        $style = "style='display:inline'";
        $class = "alert alert-danger";
    } else {
        $user = $repons[0];
        $_SESSION['id'] = $user->idUser;
        $_SESSION['user'] = $user->loginUser;
        $_SESSION['nom'] = $user->nomUser . " " . $user->prenomUser;
        $_SESSION['compte'] = $user->user_codeCompte;

        header('Location: accueil.php');
        exit;
    }
}
?>	
	
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>CONNEXION | GSF</title>
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

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
		<![endif]-->
		
	</head>
	
	<body class="login-page">
    <div class="login-box">
      <div class="login-logo">
		<i class="fa fa-lock"></i><img src="dist/img/gsf_logo.jpg"><i class="fa fa-lock"></i>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <p class="login-box-msg"><b>Démarrer une nouvelle session</b></p>
        <form action="" method="post">
          <div class="form-group has-feedback">
            <input type="text" class="form-control" placeholder="Login" name="login" id="login" required>
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input type="password" class="form-control" placeholder="Mot de passe" name="pwd" id="pwd" required>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="row">
            <div class="col-xs-4">    
                                     
            </div><!-- /.col -->
            <div class="col-xs-8">
              <button type="submit" class="btn btn-primary btn-block btn-flat" name="connect">Me connecter</button>
            </div><!-- /.col -->
          </div><br>
		  <p class="<?= $class;?>" <?= $style;?>>
			<?=$msg; ?>
		</p>
        </form>

      </div><!-- /.login-box-body -->
	  
	  <div class='lockscreen-footer text-center'>
        Copyright &copy; 2017 <b><a href="http://event24apps.com" class='text-black'>Event'24</a></b><br>
        All rights reserved
      </div>
	  
    </div><!-- /.login-box -->
	
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