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
mysql_query("SET NAMES 'utf8'");

$base_path = strstr(realpath("."),"demos",true)."lib/";
include($base_path."inc/jqgrid_dist.php");

$g = new jqgrid();

$col = array();
$col["title"] = "Id";
$col["name"] = "id"; 
$col["width"] = "10";
$cols[] = $col;		
		
// field that will be updated by autocomplete
$col = array();
$col["title"] = "client_id";
$col["name"] = "client_id"; 
$col["width"] = "10";
$col["editable"] = true;
$col["hidden"] = true;
$cols[] = $col;		
		
// normal textbox, with autocomplete enabled
$col = array();
$col["title"] = "Client";
$col["name"] = "cid";
$col["dbname"] = "clients.name"; // this is required as we need to search in name field, not id
$col["width"] = "100";
$col["align"] = "left";
$col["search"] = true;
$col["editable"] = true;
$col["formatter"] = "autocomplete"; // autocomplete
$col["formatoptions"] = array(	"sql"=>"SELECT client_id, name FROM clients",
								"search_on"=>"name", 
								"update_field" => "client_id");

// # fetch data from database, with alias k for key, v for value
// $str = $g->get_dropdown_values("select distinct client_id as k, name as v from clients");
// $col["editoptions"] = array("value"=>$str); 

$cols[] = $col;

$col = array();
$col["title"] = "Date";
$col["name"] = "invdate"; 
$col["width"] = "50";
$col["editable"] = true; // this column is editable
$col["editoptions"] = array("size"=>20); // with default display of textbox with size 20
$col["editrules"] = array("required"=>true); // and is required
$col["formatter"] = "date"; // format as date
$col["search"] = false;
$cols[] = $col;

$col = array();
$col["title"] = "Amount";
$col["name"] = "amount"; 
$col["width"] = "50";
$col["editable"] = true; // this column is editable
$col["editoptions"] = array("size"=>20); // with default display of textbox with size 20
$col["editrules"] = array("required"=>true); // and is required
$cols[] = $col;

$col = array();
$col["title"] = "Note";
$col["name"] = "note"; 
$col["width"] = "50";
$col["editable"] = true; // this column is editable
$col["editoptions"] = array("size"=>20); // with default display of textbox with size 20
$cols[] = $col;

$grid["sortname"] = 'id'; // by default sort grid by this field
$grid["sortorder"] = "desc"; // ASC or DESC
$grid["caption"] = "Invoice Data"; // caption of grid
$grid["multiselect"] = false; // allow you to multi-select through checkboxes
$grid["autowidth"] = true; // allow you to multi-select through checkboxes

$g->set_options($grid);

$g->set_actions(array(	
						"add"=>false, // allow/disallow add
						"edit"=>true, // allow/disallow edit
						"delete"=>true, // allow/disallow delete
						"rowactions"=>true, // show/hide row wise edit/del/save option
						"autofilter" => true, // show/hide autofilter for search
					) 
				);

// select query with FK_data as FK_id, e.g. clients.name as client_id
$g->select_command = "SELECT id, invdate, invheader.client_id, clients.name as cid, amount, note FROM invheader 
						INNER JOIN clients on clients.client_id = invheader.client_id
						";

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
	<link rel="stylesheet" type="text/css" media="screen" href="../../lib/js/themes/smoothness/jquery-ui.custom.css"></link>	
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