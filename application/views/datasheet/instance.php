<?php

// set up DB
$conn = mysql_connect("localhost", "root", "");
mysql_select_db("imagedata");

// set your db encoding -- for ascent chars (if required)
mysql_query("SET NAMES 'utf8'");

// include and create object
$base_path = "lib/";
include($base_path."inc/jqgrid_dist.php");

$col["width"] = "10";
$col["editable"] = TRUE; 
$col["align"] = "center"; 
$col["search"] = TRUE; 
$cols[] = $col;

$col = array();
$col["title"] = "患者名字";
$col["name"] = "PatientName";
$col["width"] = "10";
$col["editable"] = TRUE; 
$col["align"] = "center"; 
$col["search"] = TRUE; 
$cols[] = $col;

$col = array();
$col["title"] = "患者年龄";
$col["name"] = "PatientAge";
$col["width"] = "10";
$col["editable"] = TRUE; 
$col["align"] = "center"; 
$col["search"] = TRUE; 
$cols[] = $col;

$col = array();
$col["title"] = "扫描序列";
$col["name"] = "ScanID";
$col["width"] = "10";
$col["editable"] = TRUE; // this column is not editable
$col["align"] = "center"; // this column is not editable
$col["search"] = TRUE; // this column is not searchable
$cols[] = $col;

$col = array();
$col["title"] = "扫描方式";
$col["name"] = "ScanType";
$col["width"] = "5";
$col["editable"] = TRUE; // this column is not editable
$col["align"] = "center"; // this column is not editable
$col["search"] = TRUE; // this column is not searchable
$cols[] = $col;

$col = array();
$col["title"] = "扫描设备";
$col["name"] = "ScanDevice";
$col["width"] = "10";
$col["editable"] = TRUE; 
$col["align"] = "center"; 
$col["search"] = TRUE; 
$cols[] = $col;

$col = array();
$col["title"] = "扫描部位";
$col["name"] = "ScanAnatomy";
$col["width"] = "10";
$col["editable"] = TRUE; 
$col["align"] = "center"; 
$col["search"] = TRUE; 
$cols[] = $col;

$col = array();
$col["title"] = "扫描日期";
$col["name"] = "Scandate";




$g = new jqgrid();

// set few params
$grid["caption"] = "序列信息";
$g->set_options($grid);
$g->select_command = "SELECT * FROM `scansequence` s INNER JOIN `patientinfo` p ON p.`PatientID` = s.`Patient_ID`";
// set database table for CRUD operations
$g->table = "patientinfo";
$g->set_columns($cols);
// render grid
$out = $g->render("list1");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<link rel="stylesheet" type="text/css" media="screen" href="<?=base_url()?>lib/js/themes/redmond/jquery-ui.custom.css"></link>	
	<link rel="stylesheet" type="text/css" media="screen" href="<?=base_url()?>lib/js/jqgrid/css/ui.jqgrid.css"></link>	
	
	<script src="<?=base_url()?>lib/js/jquery.min.js" type="text/javascript"></script>
	<script src="<?=base_url()?>lib/js/jqgrid/js/i18n/grid.locale-en.js" type="text/javascript"></script>
	<script src="<?=base_url()?>lib/js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>	
	<script src="<?=base_url()?>lib/js/themes/jquery-ui.custom.min.js" type="text/javascript"></script>
</head>
<body>
	<div style="margin:10px">
	<?php echo $out?>
	</div>	
</body>
</html>