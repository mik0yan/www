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
$col["width"] = "10";
$cols[] = $col;		

$col = array();
$col["title"] = "Client";
$col["name"] = "name";
$col["width"] = "100";
$col["editable"] = false; // this column is not editable
$col["search"] = false; // this column is not searchable

$cols[] = $col;

$col = array();
$col["title"] = "Date";
$col["name"] = "invdate"; 
$col["width"] = "50";
$col["editable"] = true; // this column is editable
$col["editoptions"] = array("size"=>20); // with default display of textbox with size 20
$col["editrules"] = array("required"=>true); // required:true(false), number:true(false), minValue:val, maxValue:val
$col["formatter"] = "date"; // format as date
$cols[] = $col;
		
$col = array();
$col["title"] = "Total";
$col["name"] = "total";
$col["width"] = "50";
$col["editable"] = true;
// default render is textbox
$col["editoptions"] = array("value"=>'10');

$cols[] = $col;

$col = array();
$col["title"] = "Closed";
$col["name"] = "closed";
$col["width"] = "50";
$col["editable"] = true;
$col["edittype"] = "checkbox"; // render as checkbox
$col["editoptions"] = array("value"=>"Yes:No"); // with these values "checked_value:unchecked_value"
$cols[] = $col;

# Custom made column to show link, must have default value as it's not db driven
$col = array();
$col["title"] = "Logo";
$col["name"] = "logo";
$col["width"] = "40";
$col["align"] = "center";
$col["search"] = false;
$col["sortable"] = false;
# class='fancybox' to open image in fancybox
$col["default"] = "<a class='fancybox' href='http://www.raizlabs.com/blog/images/company_logos/image077.gif'><img height=25 src='http://ssl.gstatic.com/ui/v1/icons/mail/logo_default.png'></a>";
$cols[] = $col;

# Custom made column to show link, must have default value as it's not db driven
$col = array();
$col["title"] = "Details";
$col["name"] = "more_options";
$col["width"] = "30";
$col["align"] = "center";
$col["search"] = false;
$col["sortable"] = false;
# no new line in this html, only space. otherwise it may break ui of grid
# class='fancybox' data-fancybox-type='iframe' to open fancybox in iframe
$buttons_html = "<a target='_blank' class='fancybox' data-fancybox-type='iframe' href='http://jqgrid/dev/demos/editing/index.php' style='text-decoration:none; white-space:none; border:1px solid gray; padding:2px; position:relative; width:25px; color:red'>Buy</a> <a target='_blank' href='http://www.google.com?id={id}' style='text-decoration:none; white-space:none; border:1px solid gray; padding:2px; position:relative; width:25px; color:green'>Try</a>";
$col["default"] = $buttons_html;
$cols[] = $col;

# Customization of Action column width and other properties
$col = array();
$col["title"] = "Action";
$col["name"] = "act";
$col["width"] = "50";
$cols[] = $col;

$g = new jqgrid();

$grid["rowNum"] = 10; // by default 20
$grid["sortname"] = 'id'; // by default sort grid by this field
$grid["sortorder"] = "desc"; // ASC or DESC
$grid["caption"] = "Invoice Data"; // caption of grid
$grid["autowidth"] = true; // expand grid to screen width

$g->set_options($grid);

$g->set_actions(array(	
						"add"=>true, // allow/disallow add
						"edit"=>true, // allow/disallow edit
						"delete"=>true, // allow/disallow delete
						"rowactions"=>true, // show/hide row wise edit/del/save option
						"export"=>true, // show/hide export to excel option
						"autofilter" => true, // show/hide autofilter for search
						"search" => "advance" // show single/multi field search condition (e.g. simple or advance)
					) 
				);

// you can provide custom SQL query to display data
$g->select_command = "SELECT * FROM (SELECT i.id, invdate , c.name,
						i.note, i.total, i.closed FROM invheader i
						INNER JOIN clients c ON c.client_id = i.client_id) o";

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

	<!-- Add fancyBox main JS and CSS files -->
	<script type="text/javascript" src="http://www.fancyapps.com/fancybox/source/jquery.fancybox.js?v=2.1.4"></script>
	<link rel="stylesheet" type="text/css" href="http://www.fancyapps.com/fancybox/source/jquery.fancybox.css?v=2.1.4" media="screen" />

</head>
<body>
	<div style="margin:10px">
	<?php echo $out?>
	</div>
	<script>
	$(document).ready(function() {
		$('.fancybox').fancybox();
	});
	</script>
</body>
</html>