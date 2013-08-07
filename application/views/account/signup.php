<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>登录&middot; 分析平台</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="<?=base_url()?>/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
      }

      .form-signup {
        max-width: 300px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
      }
      .form-signup .form-signup-heading,
      .form-signup .checkbox {
        margin-bottom: 10px;
      }
      .form-signup input[type="text"],
      .form-signup input[type="email"],
      .form-signup input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
      }
      .form-signup input[type="repassword"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
      }

    </style>
    <link href="<?=base_url()?>bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="//cdnjs.bootcss.com/ajax/libs/html5shiv/3.6.2/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="../assets/ico/apple-touch-icon-144-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="../assets/ico/apple-touch-icon-114-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="../assets/ico/apple-touch-icon-72-precomposed.png">
		<link rel="apple-touch-icon-precomposed" href="../assets/ico/apple-touch-icon-57-precomposed.png">
		<link rel="shortcut icon" href="../assets/ico/favicon.png">
  </head>

  <body>

    <div class="container">

      <form class="form-signup" action="/index.php/account/rgtUser" method="post" accept-charset="utf-8">
        <h2 class="form-signup-heading">用户注册</h2>
      	<?php echo validation_errors(); ?>
        <input type="text" class="input-block-level" placeholder="用户名" id="username" name="username">
        <input type="email" class="input-block-level" placeholder="邮箱地址" id="email" name="email">
        <input type="password" class="input-block-level" placeholder="密码" id="password" name="password">
        <button class="btn btn-large btn-primary" type="submit">注册</button>
		<p><a href="<?=base_url()?>index.php/account/index">&gt;&gt;登录</a></p>

      </form>
    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="<?=base_url()?>bootstrap/js/jquery.js"></script>
    <script src="<?=base_url()?>bootstrap/js/bootstrap.min.js"></script>

  </body>
</html>
