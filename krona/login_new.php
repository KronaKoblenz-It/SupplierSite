<?php
include("header.php");
$pwderror = "";
$pwderrornumber = isset($_GET["error"]) ? $_GET["error"] : 0;


if(!empty($pwderrornumber)) {
    if($pwderrornumber == 1) {
        $pwderror = $str_loginerror[$lang];
    }
    if($pwderrornumber == 2) {
        $pwderror = $str_loginerror[$lang];
    }
}

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>KUANTICA</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
<!--  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">-->
  <!-- Ionicons -->
<!--  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">-->
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/iCheck/square/blue.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <!--<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>-->
  <!--<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>-->
  <![endif]-->
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
      <a href=""><p><b>KRONA KOBLENZ SPA</b></p>Area Riservata</a>

  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">

      <div style="float: left;"><img src="krona.jpg" style="height: 50px"></div>
      <div style="float: right;"><img src="krona.jpg" style="height: 50px"></div>
    <p class="login-box-msg">Autenticati per iniziare la sessione di lavoro</p>

    <br>
    <form role="form" method="post" action="enter.php">
      <div class="form-group has-feedback">
        <input type="text" class="form-control" placeholder="User" id="codice" name="codice">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control" placeholder="Password" id="password" name="password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <!-- /.col -->
        <div class="col-xs-12">
          <button type="submit" class="btn btn-primary btn-block btn-flat">Accedi</button>
        </div>
        <!-- /.col -->
      </div>
        <?php
        if(!empty($pwderror))
        {
            ?>
        <div class="row">
            <div class="col-xs-12">
                <p style="background-color: red; display: block; text-align: center;"><?php echo $pwderror; ?></p>
            </div>
        </div>
        <?php
        }
        ?>
    </form>

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.2.3 -->
<script src="plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="plugins/iCheck/icheck.min.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' // optional
    });
  });
</script>
</body>
</html>
