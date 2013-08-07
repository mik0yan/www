<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>医学影像分析平台</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="内容">
    <meta name="author" content="米宽">

    <!-- Le styles -->
    <link href="<?=base_url()?>bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
      .sidebar-nav {
        padding: 9px 0;
      }
    </style>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">医学影像分析平台</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li><a href="#">医学影像库</a></li>
              <li class="active"><a href="#">病例研究</a></li>
              <li class="dropdown" class="active">
			  	<a href="#" class="dropdown-toggle" data-toggle="dropdown">统计分析<b class="caret"></b></a>
			    <ul class="dropdown-menu">
                  <li class="nav-header">关节分析结果</li>
                  <li><a href="#">全膝关节解剖数据分析</a></li>
                  <li><a href="#">全髋关节解剖数据分析</a></li>
                  <li class="divider"></li>
                  <li class="nav-header">脊柱分析结果</li>
                  <li><a href="#">腰椎解剖数据分析</a></li>
                  <li><a href="#">胸椎解剖数据分析</a></li>
				  
                </ul>
			  </li>
            </ul>
			<?php if(isset($username)) {?>
			<p class="navbar-text pull-right">
              欢迎访问 <?php 
			  switch($usertype)
			  {
			  	case "root":
				echo "系统管理员";
				break;
				case "admin":
				echo "站点管理员";
				break;
				case "analyst":
				echo "分析员";
				break;
				case "guest":
				echo "访客";
				break;
			  };
			  
			  echo ':'.$username."&nbsp;&nbsp;&nbsp;"; ?>!  <a href="/index.php/account/signout" class="navbar-link">登出</a>
            </p>

			
			
			<?php } else {?>
            <form class="navbar-form pull-right" action="/index.php/account/verifyaccount" method="post" accept-charset="utf-8">
              <input class="span2" type="text" placeholder="用户名" id="username" name="username" >	
              <input class="span2" type="password" placeholder="密码" id="passowrd" name="password">
              <button type="submit" class="btn">登录</button>
              <button type="submit" class="btn" formaction="/index.php/account/signup">注册</button>
            </form>
			<?php }?>          
			</div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
