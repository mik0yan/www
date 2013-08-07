<?php
/**
 * PHP Grid Component
 *
 * @author Abu Ghufran <gridphp@gmail.com> - http://www.phpgrid.org
 * @version 1.4.8
 * @license: see license.txt included in package
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
$g->set_options($grid);

// set database table for CRUD operations
$g->table = "clients";

// subqueries are also supported now (v1.2)
// $g->select_command = "select * from (select * from invheader) as o";
			
// render grid
$out = $g->render("list1");

/*
js/jqgrid/js/i18n/grid.locale-en.js
js/jqgrid/js/i18n/grid.locale-fr.js
js/jqgrid/js/i18n/grid.locale-ar.js
... over 39 languages

change JS file on line 47, to your need (current is it - italian)
*/

$lang_path = strstr(realpath("."),"demos",true)."lib/js/jqgrid/js/i18n";

$cdir = scandir($lang_path);
foreach ($cdir as $key => $value)
{
  if (!in_array($value,array(".","..")))
  {
	$langs[] = $value;
  }
}

// if set from page
if (!empty($_GET["lang"]))
	$i = $_GET["lang"];
else
	$i = "grid.locale-en.js";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<link rel="stylesheet" type="text/css" media="screen" href="../../lib/js/themes/redmond/jquery-ui.custom.css"></link>	
	<link rel="stylesheet" type="text/css" media="screen" href="../../lib/js/jqgrid/css/ui.jqgrid.css"></link>	
	
	<script src="../../lib/js/jquery.min.js" type="text/javascript"></script>
	<script src="../../lib/js/jqgrid/js/i18n/<?php echo $i?>" type="text/javascript"></script>
	<script src="../../lib/js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>	
	<script src="../../lib/js/themes/jquery-ui.custom.min.js" type="text/javascript"></script>
</head>
<body>
	<div style="margin:10px">
	<form method="get">
		Language: <select name="lang" onchange="form.submit()">
		<?php foreach($langs as $k=>$t) { ?>
			<option value=<?php echo $t?> <?php echo ($i==$t)?"selected":""?>><?php echo ucwords($t)?></option>
		<?php } ?>
	</select>
	</form>
	<br>
	<?php echo $out?>
	</div>
</body>
</html>