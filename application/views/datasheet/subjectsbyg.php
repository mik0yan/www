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

$grid["grouping"] = true; //  
$grid["groupingView"] = array(); 
$grid["groupingView"]["groupField"] = array("PatientEthnic"); // specify column name to group listing 
$grid["groupingView"]["groupColumnShow"] = array(false); // either show grouped column in list or not (default: true) 
$grid["groupingView"]["groupText"] = array("<b>{0} - {1} 例</b>"); // {0} is grouped value, {1} is count in group 
$grid["groupingView"]["groupOrder"] = array("asc"); // show group in asc or desc order 
$grid["groupingView"]["groupDataSorted"] = array(true); // show sorted data within group 
$grid["groupingView"]["groupSummary"] = array(true); // work with summaryType, summaryTpl, see column: $col["name"] = "total"; 
$grid["groupingView"]["groupCollapse"] = false; // Turn true to show group collapse (default: false)  
$grid["groupingView"]["showSummaryOnHide"] = true; // show summary row even if group collapsed (hide)  

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
$g->select_command = "SELECT * FROM `patientinfo`";
// set database table for CRUD operations
$g->table = "patientinfo";
$g->set_columns($cols);
// render grid
$grid_id = "list1"; 
$out = $g->render("list1");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<link rel="stylesheet" type="text/css" media="screen" href="<?=base_url()?>lib/js/themes/redmond/jquery-ui.custom.css"></link>	
	<link rel="stylesheet" type="text/css" media="screen" href="<?=base_url()?>lib/js/jqgrid/css/ui.jqgrid.css"></link>	
	
	<script src="<?=base_url()?>lib/js/jquery.min.js" type="text/javascript"></script>
	<script src="<?=base_url()?>lib/js/jqgrid/js/i18n/grid.locale-en.js" type="text/javascript"></script>
	<script src="<?=base_url()?>lib/js/jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>	
	<script src="<?=base_url()?>	lib/js/themes/jquery-ui.custom.min.js" type="text/javascript"></script>
</head>
<body>
	<div style="margin:10px">
    分组按:  
    <select id="chngroup"> 
        <?php foreach($cols as $c) { ?> 
        <option value="<?=$c["name"] ?>"><?=$c["title"] ?></option> 
        <?php } ?> 
        <option value="clear">清除</option>  
    </select> 
    <script> 
       jQuery("#chngroup").change(function() 
        { 
            var vl = jQuery(this).val();  
            if(vl)  
            {  
                if(vl == "clear")  
                    jQuery("#<?php echo $grid_id ?>").jqGrid('groupingRemove',true);  
                else  
                    jQuery("#<?php echo $grid_id ?>").jqGrid('groupingGroupBy',vl);  
            }  
        }); 
    </script>             
    <br> 
    <br> 
		<?php echo $out?>
	</div>	
</body>
</html>