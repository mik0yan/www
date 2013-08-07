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

$g = new jqgrid();

$col = array();
$col["title"] = "Id"; // caption of column
$col["name"] = "client_id"; 
$col["width"] = "10";
$col["export"] = false;
$cols[] = $col;		

$col = array();
$col["title"] = "Client";
$col["name"] = "name";
$col["width"] = "100";
$col["align"] = "center"; // this column is not editable
$cols[] = $col;

$col = array();
$col["title"] = "Gender";
$col["name"] = "gender";
$col["width"] = "100";
$col["align"] = "center"; // this column is not editable
$cols[] = $col;

$col = array();
$col["title"] = "Company";
$col["name"] = "company";
$col["width"] = "100";
$col["align"] = "center"; // this column is not editable
$cols[] = $col;

$grid["sortname"] = 'client_id'; // by default sort grid by this field
$grid["sortorder"] = "desc"; // ASC or DESC
$grid["caption"] = "Client Data"; // caption of grid
$grid["autowidth"] = true; // expand grid to screen width
$grid["multiselect"] = false; // allow you to multi-select through checkboxes

// Predefined standard page formats: http://www.tcexam.org/doc/code/classTCPDF.html#a087d4df77e60b7054e97804069ed32c5
// Orientation: landscape, portrait
$grid["export"] = array("format"=>"pdf", "filename"=>"my-file", "heading"=>"Invoice Details", "orientation"=>"landscape", "paper"=>"a4");

// Setting RTL will export pdf as RTL also
// $grid["direction"] = "rtl";

// export filtered data or all data
$grid["export"]["range"] = "filtered"; // or "all"
$grid["export"]["paged"] = "1";
$g->set_options($grid);

// params are array(<function-name>,<class-object> or <null-if-global-func>,<continue-default-operation>)
$e["on_render_pdf"] = array("set_pdf_format", null);
$g->set_events($e);

function set_pdf_format($arr)
{
	$grid = $arr["grid"];
	$data = $arr["data"];
	
	// Use "" for html attributes values to work properly (avoid '')
	$html .= '<h1 style="border-bottom:1px solid black">My Custom '.$grid->options['export']['heading'].'</h1>';
	$html .= '<table border="1" cellpadding="2" cellspacing="0">';
	
	$i = 0;
	foreach($data as $v)
	{
		$shade = ($i++ % 2) ? 'bgcolor="aliceblue"' : '';
		$html .= '<tr>';
		foreach($v as $d)
		{
			// bold header
			if  ($i == 1)
				$html .= "<td bgcolor=\"lightgrey\"><strong>$d</strong></td>";
			else		
				$html .= "<td $shade>$d</td>";	
		}
			
		$html .= '</tr>';
	}
	$html .= '</table>';	

	return $html;
}


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

// this db table will be used for add,edit,delete
$g->table = "clients";

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
	<br>
	<?php echo $out?>
	</div>
</body>
</html>