<?php
/**
 * PHP Grid Component
 *
 * @author Abu Ghufran <gridphp@gmail.com> - http://www.phpgrid.org
 * @version 1.4.8
 * @license: see license.txt included in package
 */

// NOTE: Default edit mode only work with inline editing

// set up DB
$conn = mysql_connect("localhost", "root", "");
mysql_select_db("griddemo");

// set your db encoding -- for ascent chars (if required)
mysql_query("SET NAMES 'utf8'");

// include and create object
$base_path = "../../lib/";
include($base_path."inc/jqgrid_dist.php");
$g = new jqgrid();

// set few params
$grid["caption"] = "Sample Grid";
$g->set_options($grid);

// set database table for CRUD operations
$g->table = "invheader";


$col = array();
$col["title"] = "Id";
$col["name"] = "id"; 
$col["width"] = "10";
$cols[] = $col;		

$col = array();
$col["title"] = "Date";
$col["name"] = "invdate"; 
$col["width"] = "50";
$col["editable"] = true;
$col["editoptions"] = array("size"=>20); // with default display of textbox with size 20
$cols[] = $col;
		
$col = array();
$col["title"] = "Note";
$col["name"] = "note";
$col["sortable"] = false; // this column is not sortable
$col["search"] = false; // this column is not searchable
$col["editable"] = true;
$col["editoptions"] = array("rows"=>2, "cols"=>50); // with these attributes
$cols[] = $col;

$col = array();
$col["title"] = "Total";
$col["name"] = "total";
$col["width"] = "50";
$col["editable"] = true;
$cols[] = $col;

$col = array();
$col["title"] = "Closed";
$col["name"] = "closed";
$col["width"] = "50";
$col["editable"] = true;
$col["edittype"] = "checkbox"; // render as checkbox
$col["editoptions"] = array("value"=>"1:0"); // with these values "checked_value:unchecked_value"
$cols[] = $col;

// pass the cooked columns to grid
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
	<script>
	var opts = {

		'loadComplete': function () {
		    var $this = $(this), ids = $this.jqGrid('getDataIDs'), i, l = ids.length;
		    for (i = 0; i < l; i++) {
		        // list1 is the name of grid, passed in ->render() function
		        id = ids[i];
				jQuery('#list1').editRow(id, true, function(){}, function(){

														if (jQuery('#edit_row_list1_').val() != undefined)
														{
															jQuery('#edit_row_list1_'+id).show();
															jQuery('#save_row_list1_'+id).hide();
														}
														return true;

													},null,null,function(){
													},null,
													function(){

														if (jQuery('#edit_row_list1_').val() != undefined)
														{
															jQuery('#edit_row_list1_'+id).show();
															jQuery('#save_row_list1_'+id).hide();
														}
														return true;
													}
							); 
				
				jQuery('#edit_row_list1_'+id).hide();
				jQuery('#save_row_list1_'+id).show();
		    }
		}
	};
	</script>
	<div style="margin:10px">
	<?php echo $out?>
	</div>	
</body>
</html>