<?php
if (!empty($_GET["file"]))
{
	$f = $_GET["file"];
	
	$f = str_replace(".php","",$f);
	
	// remote file inclusion attempt fix
	if (strpos($f,".")!==false)
		die("+1 for you");
		
	$f = "demos/$f.php";

	if (!file_exists($f))
		die("+1 for you");

	$code = file_get_contents($f);
	
	// removed db settings
	$code = preg_replace("/mysql_connect(.*)/i","mysql_connect('localhost','root','');",$code);
	
	highlight_string($code);
	echo "<br>&nbsp;";
	die;
}	
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>PHP Grid Control Demos | www.phpgrid.org</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
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
          <a class="brand" href="#">PHP Grid Control Demos</a>
          <div class="nav-collapse collapse">
            <p class="navbar-text pull-right">
              <a href="http://www.phpgrid.org" class="navbar-link">www.phpgrid.org</a>
            </p>
            <ul class="nav">
              <li><a href="http://www.phpgrid.org">Home</a></li>
              <li class="active"><a href="#">Demos</a></li>
              <li><a target="_blank" href="http://www.phpgrid.org/faqs">FAQ</a></li>
              <li><a target="_blank" href="http://www.phpgrid.org/docs">Docs</a></li>
              <li><a href="#contact">Contact</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

	<?php 
	function dirToArray($dir) 
	{
		$result = array();
		$cdir = scandir($dir);
		foreach ($cdir as $key => $value)
		{
		  if (!in_array($value,array(".","..","temp")) && strpos($value,"_") === false)
		  {
			 if (is_dir($dir . DIRECTORY_SEPARATOR . $value))
			 {
				$result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
			 }
			 else
			 {
				$result[] = $value;
			 }
		  }
		}

		return $result;
	}
	$samples = dirToArray("demos");
	?>
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span2">
			<div class="accordion" id="accordion_menu">
					<?php 
					foreach($samples as $k=>$v) 
					{
						if (is_numeric($k)) continue;
						$folder = ucwords($k);
						?>
						<div class="accordion-group">
						<div class="accordion-heading">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion_menu" href="#collapse<?php echo $k?>">
						  <strong><?php echo $folder;?></strong>
						</a>
						</div>	
						<div id="collapse<?php echo $k?>" class="accordion-body collapse">
							<div class="accordion-inner">
								<?php
								foreach($v as $f) 
								{
									$fname = str_replace(".php","",$f);
									$fname = str_replace("-"," ",$fname);
									$fname = ucwords($fname);
									echo "<a href='demos/$k/$f' onclick=\"jQuery('#code').load('index.php?file=/$k/$f'); $('#grid-demo-tabs a:first').tab('show');\" target='demo_frame'><i class='icon-chevron-right'></i> $fname </a><br/>";
								}
								?>
							</div>
						</div>				
						</div>				
						<?php
					}
					?>
			</div>
		  
		  
        </div><!--/span-->
		
        <div class="span10">
          <div class="row-fluid">
            <div class="span12">
			
				<ul class="nav nav-tabs" id="grid-demo-tabs">
					<li class="active"><a href="#demo" data-toggle="tab">Demo</a></li>
					<li><a href="#code" data-toggle="tab">Code</a></li>
				</ul>
				
				<div class="tab-content" id="grid-demo-tabs-content">
				  
					<div id="demo" class="tab-pane fade in active">
					<iframe onload="iframeLoaded(this)" name="demo_frame" frameborder="0" width="100%" height="400" src="demos/editing/index.php"></iframe>
					</div>
				  
					<div id="code" class="tab-pane fade">
					</div>
				  
				</div>

            </div><!--/span-->
          </div><!--/row-->
        </div><!--/span-->
		
		<div class="row-fluid">
			<div class="span12">
			  <div class="row-fluid">
				<div class="alert alert-info">
					<a name="contact"></a>
				  <h2>Contact</h2>
				  <p class="text-info">For technical support query, ask at our <a href="http://www.phpgrid.org/support">Support Center</a> </p>
				</div><!--/span-->
			  </div><!--/row-->
			</div><!--/span-->
		  </div><!--/row-->
		  
      </div><!--/row-->

      <hr>
		<!-- Le javascript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="bootstrap/js/jquery.js"></script>
		<script src="bootstrap/js/bootstrap.min.js"></script>
		<script>
		
			$('#grid-demo-tabs a').click(function (e) {
			e.preventDefault();
			$(this).tab('show');
			})
			
			jQuery('#code').load('index.php?file=/editing/index.php');

			function iframeLoaded(iFrameID) 
			{
			  if(iFrameID) {
			        // here you can meke the height, I delete it first, then I make it again
			        iFrameID.height = "";
			        iFrameID.height = iFrameID.contentWindow.document.body.scrollHeight + 20 + "px";
			  }
			}
		</script>
      <footer>
        <p>&copy; <a href="http://www.phpgrid.org">www.phpgrid.org</a> <?php echo date("Y");?></p>
      </footer>

    </div><!--/.fluid-container-->


  </body>
</html>
