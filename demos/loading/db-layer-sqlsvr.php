<?php
/**
 * PHP Grid Component
 *
 * @author Abu Ghufran <gridphp@gmail.com> - http://www.phpgrid.org
 * @version 1.4.8
 * @license: see license.txt included in package
 */

/**
 * To support non-mysql databases (even mysql), see adodb lib documentation below:
 * http://phplens.com/lens/adodb/docs-adodb.htm#connect_ex
 * http://phplens.com/lens/adodb/docs-adodb.htm#drivers
 */
$db_conf = array();
$db_conf["type"] = "mssqlnative"; // or mssql
$db_conf["server"] = "(local)\sqlexpress"; // ip:port
$db_conf["user"] = null;
$db_conf["password"] = null;
$db_conf["database"] = "master";

/*
# more options 

$db_conf = array();
$db_conf["type"] = "odbc_mssql"; 
$db_conf["server"] = "Driver={SQL Server};Server=localhost;Database=northwind;";
$db_conf["user"] = "user";
$db_conf["password"] = "pass";
$db_conf["database"] = null;

$db_conf = array();
$db_conf["type"] = "ado_mssql"; 
$db_conf["server"] = "PROVIDER=MSDASQL;DRIVER={SQL Server};SERVER=flipper;DATABASE=ai;UID=sa;PWD=;";
$db_conf["user"] = null;
$db_conf["password"] = null;
$db_conf["database"] = null;
*/
		 
// include and create object
$base_path = strstr(realpath("."),"demos",true)."lib/";
include($base_path."inc/jqgrid_dist.php");
$g = new jqgrid($db_conf);

// set few params
$grid["caption"] = "Sample Grid";
$grid["rowNum"] = 5;
$g->set_options($grid);
$g->set_actions(array("inlineadd"=>true));

// set database table for CRUD operations
$g->table = "[msdb].[dbo].[syscategories]";

// subqueries are also supported now (v1.2)
// $g->select_command = "select * from (select * from invheader) as o";

// render grid
$out = $g->render("list1");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<link rel="stylesheet" type="text/css" media="screen" href="../../lib/js/themes/redmond/jquery-ui.custom.css"></link>	
	<link rel="stylesheet" type="text/css" media="screen" href="../../lib/js/jqgrid/css/ui.jqgrid.css"></link>	
	
	<script src="../../lib/js/jquery.min.js" type="text/javascript"></script>
	<script src="../../lib/js/jqgrid/js/i18n/grid.locale-en.js" type="text/javascript"></script>
	<script src="../../lib/js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>	
	<script src="../../lib/js/themes/jquery-ui.custom.min.js" type="text/javascript"></script>
</head>
<body>
	You must have SQL Server installed for this demo. Also review the authentication by reviewing file loading/db-layer.php
	<div style="margin:10px">
	<?php echo $out?>
	</div>
</body>
</html>