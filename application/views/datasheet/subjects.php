<?php

// set up DB
$conn = mysql_connect("localhost", "root", "");
mysql_select_db("imagedata");

// set your db encoding -- for ascent chars (if required)
mysql_query("SET NAMES 'utf8'");

// include and create object
$base_path = "lib/";
include($base_path."inc/jqgrid_dist.php");


$col = array();
$col["title"] = "患者编号";
$col["name"] = "PatientID";
$col["width"] = "10";
$col["editable"] = TRUE; // this column is not editable
$col["align"] = "center"; // this column is not editable
$col["search"] = TRUE; // this column is not searchable
$cols[] = $col;

$col = array();
$col["title"] = "姓名";
$col["name"] = "PatientName";
$col["width"] = "10";
$col["editable"] = TRUE; // this column is not editable
$col["align"] = "center"; // this column is not editable
$col["search"] = TRUE; // this column is not searchable
$cols[] = $col;

$col = array();
$col["title"] = "性别";
$col["name"] = "PatientSex";
$col["width"] = "10";
$col["editable"] = TRUE; 
$col["align"] = "center"; 
$col["search"] = TRUE; 
$cols[] = $col;

$col = array();
$col["title"] = "年龄";
$col["name"] = "PatientAge";
$col["width"] = "10";
$col["editable"] = TRUE; 
$col["align"] = "center"; 
$col["search"] = TRUE; 
$cols[] = $col;

$col = array();
$col["title"] = "民族";
$col["name"] = "PatientEthnic";
$col["width"] = "10";
$col["editable"] = TRUE; 
$col["align"] = "center"; 
$col["search"] = TRUE; 
$cols[] = $col;

$col = array();
$col["title"] = "籍贯";
$col["name"] = "PatientNativePlace";
$col["width"] = "10";
$col["editable"] = TRUE; 
$col["align"] = "center"; 
$col["search"] = TRUE; 
$cols[] = $col;

$col = array();
$col["title"] = "身高";
$col["name"] = "PatientHeight";
$col["width"] = "10";
$col["editable"] = TRUE; 
$col["align"] = "center"; 
$col["search"] = TRUE; 
$cols[] = $col;

$col = array();
$col["title"] = "体重";
$col["name"] = "PatientWeight";
$col["width"] = "10";
$col["editable"] = TRUE; 
$col["align"] = "center"; 
$col["search"] = TRUE; 
$cols[] = $col;

$col = array();
$col["title"] = "生日";
$col["name"] = "PatientBirthDate";
$col["width"] = "10";
$col["editable"] = TRUE; 
$col["align"] = "center"; 
$col["search"] = TRUE; 
$cols[] = $col;

$col = array();
$col["title"] = "诊断信息";
$col["name"] = "PatientDiagnosisInfo";
$col["width"] = "30";
$col["editable"] = TRUE; 
$col["align"] = "center"; 
$col["search"] = TRUE; 
$cols[] = $col;

$g = new jqgrid();

// set few params
$grid["caption"] = "患者信息";
$g->set_options($grid);

// set database table for CRUD operations
$g->table = "patientinfo";
$g->set_columns($cols);
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
	<div style="margin:10px">
	<?php echo $out?>
	</div>	
</body>
</html>