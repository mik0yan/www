<?php
/**
 * PHP Grid Component
 *
 * @author Abu Ghufran <gridphp@gmail.com> - http://www.phpgrid.org
 * @version 1.4.8
 * @license: see license.txt included in package
 */

// include and create object
$base_path = strstr(realpath("."),"demos",true)."lib/";
include($base_path."inc/jqgrid_dist.php");
$g = new jqgrid();

// set few params
$grid["caption"] = "Loading data from Array";
$grid["multiselect"] = true;
$g->set_options($grid);

$name = array('Pepsi 1.5 Litre', 'Sprite 1.5 Litre', 'Cocacola 1.5 Litre', 'Dew 1.5 Litre', 'Nestle 1.5 Litre');
for ($i = 0; $i < 200; $i++)
{
    $data[$i]['id']    = $i+1;
    $data[$i]['code']    = $name[rand(0, 4)][0].($i+5);
    $data[$i]['name']    = $name[rand(0, 4)];
    $data[$i]['cost']    = rand(0, 100)." USD";
    $data[$i]['quantity'] = rand(0, 100);
    $data[$i]['discontinued'] = rand(0, 1);
    $data[$i]['email'] = 'buyer_'. rand(0, 100) .'@google.com';
    $data[$i]['notes'] = '';
}

// pass data in table param for local array grid display
$g->table = $data;

// If you want to customize columns params
$col = array();
$col["title"] = "Id"; // caption of column
$col["name"] = "id"; 
$col["width"] = "10";
$cols[] = $col;		

$col = array();
$col["title"] = "Code"; // caption of column
$col["name"] = "code"; 
$col["width"] = "10";
$cols[] = $col;	

$col = array();
$col["title"] = "Name"; // caption of column
$col["name"] = "name"; 
$col["width"] = "40";
$cols[] = $col;

$col = array();
$col["title"] = "Cost"; // caption of column
$col["name"] = "cost"; 
$col["width"] = "10";
$cols[] = $col;

$col = array();
$col["title"] = "Quantity"; // caption of column
$col["name"] = "quantity"; 
$col["width"] = "10";
$cols[] = $col;		

$col = array();
$col["title"] = "Email"; // caption of column
$col["name"] = "email"; 
$col["width"] = "40";
$col["formatter"] = "function(cellvalue, options, rowObject){ return my_custom_link(cellvalue, options, rowObject);}";
$cols[] = $col;		

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
	function my_custom_link(cellvalue, options, rowObject)
	{
		return '<a href="mailto:'+cellvalue+'">'+cellvalue+'</a>';
	}
	</script>
	<div style="margin:10px">
	<?php echo $out?>
	</div>
</body>
</html>