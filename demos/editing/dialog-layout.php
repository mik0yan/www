<?php
/**
 * PHP Grid Component
 *
 * @author Abu Ghufran <gridphp@gmail.com> - http://www.phpgrid.org
 * @version 1.4.8
 * @license: see license.txt included in package
 */

/**
 * Grid with 2 column layout, implemented tabindex for vertical tab focusing
 * and custom width & height of dialog boxes
 */

// set up DB
$conn = mysql_connect("localhost", "root", "");
mysql_select_db("griddemo");

// set your db encoding -- for ascent chars (if required)
mysql_query("SET NAMES 'utf8'");

// include and create object
$base_path = strstr(realpath("."),"demos",true)."lib/";
include($base_path."inc/jqgrid_dist.php");
$g = new jqgrid();

// set few params
$grid["caption"] = "Sample Grid";
$grid["multiselect"] = true;

$grid["export"]["range"] = "filtered"; // or "all"

// # set add/edit dialog width ... to apply css (see below)

$grid["add_options"] = array("recreateForm" => true, "closeAfterEdit"=>true, 'width'=>'420', 'top'=>'200', 'left'=>'200');
$grid["edit_options"] = array("recreateForm" => true, "closeAfterEdit"=>true, 'width'=>'420', 'top'=>'200', 'left'=>'200');

$g->set_options($grid);

// set database table for CRUD operations
$g->table = "clients";

$col = array();
$col["title"] = "Id"; // caption of column
$col["name"] = "client_id"; 
$col["width"] = "20";
$col["editable"] = true;
$col["formoptions"] = array("rowpos"=>"1", "colpos"=>"1");
$col["editoptions"] = array("tabindex"=>"100");
$cols[] = $col;	

$col = array();
$col["title"] = "Name"; // caption of column
$col["name"] = "name"; 
$col["editable"] = true;
$col["formoptions"] = array("rowpos"=>"1", "colpos"=>"2");
$col["editoptions"] = array("tabindex"=>"102");
$cols[] = $col;	

$col = array();
$col["title"] = "Gender"; // caption of column
$col["name"] = "gender"; 
$col["width"] = "30";
$col["editable"] = true;
$col["formoptions"] = array("rowpos"=>"2", "colpos"=>"1");
$col["editoptions"] = array("tabindex"=>"101");
$cols[] = $col;	

$col = array();
$col["title"] = "Company"; // caption of column
$col["name"] = "company"; 
$col["editable"] = true;
$col["formoptions"] = array("rowpos"=>"2", "colpos"=>"2");
$col["editoptions"] = array("tabindex"=>"103");
$col["edittype"] = "textarea"; 
$col["editoptions"] = array("rows"=>2, "cols"=>20); 
$cols[] = $col;	

$g->set_columns($cols);

$g->set_actions(array(	
						"add"=>true, // allow/disallow add
						"edit"=>true, // allow/disallow edit
						"delete"=>true, // allow/disallow delete
						"rowactions"=>true, // show/hide row wise edit/del/save option
						"export_excel"=>true, // show/hide export to excel option - must set export xlsx params
						"export_pdf"=>true, // show/hide export to pdf option - must set pdf params
						"autofilter" => true, // show/hide autofilter for search
						"search" => "advance" // show single/multi field search condition (e.g. simple or advance)
					) 
				);
			
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
	<?php /* css for add/edit dialog editing */ ?>
	<style>
		/* Alternate way if we dont use formoptions */
		/*
		.FormGrid .EditTable .FormData
		{
			float: left;
			width: 200px;
		}
	   */
		.FormGrid .EditTable .FormData .CaptionTD
		{
			width: 50px;
			vertical-align: top;
			padding-top: 5px;
		}
		.FormGrid .EditTable .FormData .DataTD #closed
		{
			width: 25px;
		}
	</style>
	
	<div style="margin:10px">
	<?php echo $out?>
	</div>
</body>
</html>