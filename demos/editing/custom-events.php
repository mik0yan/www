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

$grid = new jqgrid();

$opt["caption"] = "Clients Data";
$grid->set_options($opt);

## ------------------ ##
## SERVER SIDE EVENTS ##
## ------------------ ##

// params are array(<function-name>,<class-object> or <null-if-global-func>,<continue-default-operation>)
// if you pass last argument as true, functions will act as a data filter, and insert/update will be performed by grid
$e["on_insert"] = array("add_client", null, false);
$e["on_update"] = array("update_client", null, false);
$e["on_delete"] = array("delete_client", null, true);
# $e["on_after_insert"] = array("after_insert", null, true); // return last inserted id for further working
$e["on_data_display"] = array("filter_display", null, true);

## ------------------ ##
## CLIENT SIDE EVENTS ##
## ------------------ ##
// just set the JS function name (should exist)
$e["js_on_select_row"] = "do_onselect";

$grid->set_events($e);

function update_client($data)
{
	/*
		These comments are just to show the input param format

		$data => Array
		(
			[client_id] => 2
			[params] => Array
				(
					[client_id] => 2
					[name] => Client 2
					[gender] => male
					[company] => Client 2 Company
				)

		)
	*/
	$str = "UPDATE clients SET name='My custom {$data["params"]["name"]}'
					WHERE client_id = {$data["client_id"]}";
	mysql_query($str);
}

function delete_client($data)
{
	/*
		These comments are just to show the input param format
		$data => Array
		(
			[client_id] => 2
		)
	*/
}

function add_client($data)
{
	mysql_query("INSERT INTO clients VALUES (null,'{$data["params"]["name"]}','{$data["params"]["gender"]}','{$data["params"]["company"]}')");
	/*
		These comments are just to show the input param format
		$data => Array
			(
				[params] => Array
					(
						[client_id] => 
						[name] => Test
						[gender] => male
						[company] => Comp
					)

			)

	*/
}

/**
 * Just update the passed argument, as it is passed by reference
 * Changes will be reflected in grid
 */
function filter_display($data)
{
	/*
	These comments are just to show the input param format
	Array
	(
	    [params] => Array
	        (
	            [0] => Array
	                (
	                    [client_id] => 1
	                    [name] => Client 1
	                    [gender] => My custom malea
	                    [company] => My custom Client 1 Company 1
	                )

	            [1] => Array
	                (
	                    [client_id] => 2
	                    [name] => Client 2
	                    [gender] => male
	                    [company] => Client 2 Com2pany 11
	                )
				
				.......
	*/
	foreach($data["params"] as &$d)
	{
		$d["gender"] = strtoupper($d["gender"]);
	}
}


$grid->table = "clients";
$out = $grid->render("list1");
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
	function do_onselect(id)
	{
		var rd = jQuery('#list1').jqGrid('getCell', id, 'company'); // where invdate is column name
		jQuery("#span_extra").html(rd);
	}
	</script>
	<div style="margin:10px">
	Custom events example ... 
	<br>
	<br>
	<?php echo $out?>
	<br>
	Company: <span id="span_extra">Not Selected</span>
	</div>
</body>
</html>