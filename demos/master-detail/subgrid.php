<?php 
/**
 * PHP Grid Component
 *
 * @author Abu Ghufran <gridphp@gmail.com> - http://www.phpgrid.org
 * @version 1.4.8
 * @license: see license.txt included in package
 */
 
error_reporting(E_ALL & ~E_NOTICE);

$conn = mysql_connect("localhost", "root", "");
mysql_select_db("griddemo");

$base_path = strstr(realpath("."),"demos",true)."lib/";
include($base_path."inc/jqgrid_dist.php");

$grid = new jqgrid();

$opt["caption"] = "Clients Data";

// following params will enable subgrid -- by default 'rowid' (PK) of parent is passed
$opt["subGrid"] = true;
$opt["subgridurl"] = "subgrid_detail.php";

// $opt["subgridparams"] = "name,gender,company"; // comma sep. fields. will be POSTED from parent grid to subgrid, can be fetching using $_POST in subgrid
$grid->set_options($opt);

$grid->table = "clients";
$out = $grid->render("list1");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<link rel="stylesheet" type="text/css" media="screen" href="../../lib/js/themes/start/jquery-ui.custom.css"></link>	
	<link rel="stylesheet" type="text/css" media="screen" href="../../lib/js/jqgrid/css/ui.jqgrid.css"></link>	
	
	<script src="../../lib/js/jquery.min.js" type="text/javascript"></script>
	<script src="../../lib/js/jqgrid/js/i18n/grid.locale-en.js" type="text/javascript"></script>
	<script src="../../lib/js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>	
	<script src="../../lib/js/themes/jquery-ui.custom.min.js" type="text/javascript"></script>
</head>
<body>
	<div style="margin:10px">
	Subgrid example ... this file will load subgrid defined in 'subgrid_detail.php'
	<br>
	<br>
	<?php echo $out?>
	</div>
</body>
</html>