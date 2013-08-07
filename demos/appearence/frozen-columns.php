<?php 
/**
 * PHP Grid Component
 *
 * @author Abu Ghufran <gridphp@gmail.com> - http://www.phpgrid.org
 * @version 1.4.8
 * @license: see license.txt included in package
 */

$conn = mysql_connect("localhost", "root", "");
mysql_select_db("griddemo");

$base_path = strstr(realpath("."),"demos",true)."lib/";
include($base_path."inc/jqgrid_dist.php");

// you can customize your own columns ...

$col = array();
$col["title"] = "Id"; // caption of column
$col["name"] = "id"; // grid column name, must be exactly same as returned column-name from sql (tablefield or field-alias)
$col["width"] = "20";
$col["frozen"] = true;
# $col["hidden"] = true; // hide column by default
$cols[] = $col;		

$col = array();
$col["title"] = "Client";
$col["name"] = "name";
$col["editable"] = false; // this column is not editable
$col["align"] = "center"; // this column is not editable
$col["search"] = false; // this column is not searchable
$col["frozen"] = true;
$cols[] = $col;

$col = array();
$col["title"] = "Note";
$col["name"] = "note";
$col["sortable"] = false; // this column is not sortable
$col["search"] = false; // this column is not searchable
$col["editable"] = true;
$col["edittype"] = "textarea"; // render as textarea on edit
$col["editoptions"] = array("rows"=>2, "cols"=>20); // with these attributes
// don't show this column in list, but in edit/add mode
$col["hidden"] = true;
$col["editrules"] = array("edithidden"=>true); 

$cols[] = $col;

$col = array();
$col["title"] = "Date";
$col["name"] = "invdate"; 
$col["editable"] = true; // this column is editable
$col["editoptions"] = array("size"=>20); // with default display of textbox with size 20
$col["editrules"] = array("required"=>true); // required:true(false), number:true(false), minValue:val, maxValue:val
$col["formatter"] = "date"; // format as date
$cols[] = $col;
		
		
$col = array();
$col["title"] = "Total";
$col["name"] = "total";
$col["editable"] = true;
// default render is textbox
$col["editoptions"] = array("value"=>'10');

$cols[] = $col;

$col = array();
$col["title"] = "Closed";
$col["name"] = "closed";
$col["editable"] = true;
$col["edittype"] = "checkbox"; // render as checkbox
$col["editoptions"] = array("value"=>"Yes:No"); // with these values "checked_value:unchecked_value"
$cols[] = $col;


$col = array();
$col["title"] = "Date";
$col["name"] = "invdate"; 
$col["editable"] = true; // this column is editable
$col["editoptions"] = array("size"=>20); // with default display of textbox with size 20
$col["editrules"] = array("required"=>true); // required:true(false), number:true(false), minValue:val, maxValue:val
$col["formatter"] = "date"; // format as date
$cols[] = $col;
		
		
$col = array();
$col["title"] = "Total";
$col["name"] = "total";
$col["editable"] = true;
// default render is textbox
$col["editoptions"] = array("value"=>'10');

$cols[] = $col;

$col = array();
$col["title"] = "Closed";
$col["name"] = "closed";
$col["editable"] = true;
$col["edittype"] = "checkbox"; // render as checkbox
$col["editoptions"] = array("value"=>"Yes:No"); // with these values "checked_value:unchecked_value"
$cols[] = $col;

# Customization of Action column width and other properties
$col = array();
$col["title"] = "Action";
$col["name"] = "act";
$col["width"] = "150";
$cols[] = $col;

$g = new jqgrid();

// $grid["url"] = ""; // your paramterized URL -- defaults to REQUEST_URI
$grid["rowNum"] = 10; // by default 20
$grid["sortname"] = 'id'; // by default sort grid by this field
$grid["sortorder"] = "desc"; // ASC or DESC
$grid["caption"] = "Invoice Data"; // caption of grid

$grid["autowidth"] = true; // expand grid to screen width
$grid["multiselect"] = true; // allow you to multi-select through checkboxes
$grid["rowactions"] = true;

$grid["shrinkToFit"] = false; // dont shrink to fit on screen
$grid["sortable"] = false; // it is required for freezed column feature


$g->set_options($grid);

$g->set_actions(array(	
						"add"=>true, // allow/disallow add
						"edit"=>true, // allow/disallow edit
						"delete"=>true, // allow/disallow delete
						"rowactions"=>true, // show/hide row wise edit/del/save option
						"autofilter" => true, // show/hide autofilter for search
					) 
				);

// you can provide custom SQL query to display data
$g->select_command = "SELECT i.id, invdate , c.name,
						i.note, i.total, mod(i.total,100) as bar, i.closed FROM invheader i
						INNER JOIN clients c ON c.client_id = i.client_id";

// this db table will be used for add,edit,delete
$g->table = "invheader";

// pass the cooked columns to grid
$g->set_columns($cols);

// generate grid output, with unique grid name as 'list1'
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
	Refresh page to change themes randomly. You can also have your customized theme (jqueryui.com/themeroller).
	<br>
	<br>
	<?php echo $out?>
	</div>
</body>
</html>