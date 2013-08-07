<?php
/**
 * PHP Grid Component
 *
 * @author Abu Ghufran <gridphp@gmail.com> - http://www.phpgrid.org
 * @version 1.4.8
 * @license: see license.txt included in package
 */
 
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set("display_errors","off");

class jqgrid
{
	// grid parameters
	var $options = array();
	
	// select query to show data
	var $select_command;
	
	// db table name used in add,update,delete
	var $table;
	
	// allowed operation on grid
	var $actions;

	// var for conditional css data
	var $conditional_css;

	// show server error
	var $debug;
	
	// db connection identifier - not used now, @todo: need to integrate adodb lib
	var $con;
	var $db_driver;
		
	// callback events
	var $events;
	
	/**
	 * Contructor to set default params
	 */
	function jqgrid($db_conf = null)
	{
		if (!isset($_SESSION) || !is_array($_SESSION))
			session_start();

		$this->db_driver = "mysql";
		$this->debug = 1;
		// shown in case of debug = 0
		$this->error_msg = "Some issues occured in this operation, Contact technical support for help";
		
		// use adodb layer to support non-mysql dbs
		if ($db_conf)
		{
			// set up DB
			include("adodb/adodb.inc.php");
			$driver = $db_conf["type"];
			$this->con = ADONewConnection($driver); # eg. 'mysql,oci8(for oracle),mssql,postgres,sybase' 
			$this->con->SetFetchMode(ADODB_FETCH_ASSOC);
			$this->con->debug = 0;
	
			$this->con->Connect($db_conf["server"], $db_conf["user"], $db_conf["password"], $db_conf["database"]);
	
			// set your db encoding -- for ascent chars (if required)	
			if ($db_conf["type"] == "mysql")
				$this->con->Execute("SET NAMES 'utf8'");
		
			$this->db_driver = $db_conf["type"];
		}
		
		$grid["datatype"] = "json";
		$grid["rowNum"] = 20;
		$grid["width"] = 900;
		$grid["height"] = 350;
		$grid["rowList"] = array(10,20,30);
		$grid["viewrecords"] = true;
		$grid["scrollrows"] = true;
		$grid["toppager"] = false;
		// renamed qstr variable due to wordpress conflict
		$grid["prmNames"] = array("page"=>"jqgrid_page");

		// default sort options (first field and asc)
		$grid["sortname"] = "1";
		$grid["sortorder"] = "asc";
		$grid["form"]["nav"] = false;
		
		$protocol = ( ($_SERVER['HTTPS'] == "on" || $_SERVER["SERVER_PORT"] == "443" ) ? "https" : "http");
		$grid["url"] = "$protocol://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
		
		// pass subgrid params if exist
		$s = (strstr($grid["url"], "?")) ? "&":"?";
		if (isset($_REQUEST["rowid"]) && isset($_REQUEST["subgrid"]))
			$grid["url"] .= $s."rowid=".$_REQUEST["rowid"]."&subgrid=".$_REQUEST["subgrid"];
		
		// persist rowid in session and get (for export fix master-detail's detail grid case)
		if (!empty($_GET["rowid"]))
			$_SESSION["rowid"] = $_GET["rowid"];
		$_GET["rowid"] = $_SESSION["rowid"];

		$grid["editurl"] = $grid["url"];
		$grid["cellurl"] = $grid["url"];
		
		// virtual scrolling, for big datasets
		$grid["scroll"] = 0;
		$grid["sortable"] = true;
		$grid["cellEdit"] = false;

		// if specific export is requested
		if (isset($_GET["export_type"]) && ($_GET["export_type"] == "xlsx" || $_GET["export_type"] == "excel"))
			$grid["export"]["format"] = "excel";
		else if (isset($_GET["export_type"]) && $_GET["export_type"] == "pdf")
			$grid["export"]["format"] = "pdf";

		// default pdf export options
		$grid["export"]["paper"] = "a4";
		$grid["export"]["orientation"] = "landscape";
		
		$grid["add_options"] = array("recreateForm" => true, "closeAfterAdd"=>true, 
										"errorTextFormat"=> "function(r){ return r.responseText;}"
										);
		$grid["edit_options"] = array("recreateForm" => true, "closeAfterEdit"=>true, 
										"errorTextFormat" => "function(r){ return r.responseText;}"
										);
		$grid["delete_options"] = array("errorTextFormat"=> "function(r){ return r.responseText;}"
										);
		
		$this->options = $grid;	
		
		$this->actions["showhidecolumns"] = false;
		$this->actions["inlineadd"] = false;
		$this->actions["search"] = "";
		$this->actions["export"] = false;
	}

	/**
	 * Helping function to parse array
	 */
	private function strip($value)
	{
		if(get_magic_quotes_gpc() != 0)
		{
			if(is_array($value))  
				if ( array_is_associative($value) )
				{
					foreach( $value as $k=>$v)
						$tmp_val[$k] = stripslashes($v);
					$value = $tmp_val; 
				}				
				else  
					for($j = 0; $j < sizeof($value); $j++)
						$value[$j] = stripslashes($value[$j]);
			else
				$value = stripslashes($value);
		}
		return $value;
	}	
	
	/**
	 * Advance search where clause maker
	 */
	private function construct_where($s)
	{
		$qwery = "";
		//['eq','ne','lt','le','gt','ge','bw','bn','in','ni','ew','en','cn','nc']
		$qopers = array(
					  'eq'=>" = ",
					  'ne'=>" <> ",
					  'lt'=>" < ",
					  'le'=>" <= ",
					  'gt'=>" > ",
					  'ge'=>" >= ",
					  'bw'=>" LIKE ",
					  'bn'=>" NOT LIKE ",
					  'in'=>" IN ",
					  'ni'=>" NOT IN ",
					  'ew'=>" LIKE ",
					  'en'=>" NOT LIKE ",
					  'cn'=>" LIKE " ,
					  'nc'=>" NOT LIKE " );
		if ($s) {
			$jsona = json_decode($s,true);
			if(is_array($jsona))
			{
				$gopr = $jsona['groupOp'];
				$rules = $jsona['rules'];
				$i =0;
				foreach($rules as $key=>$val) 
				{
					# fix for conflicting table name fields (used alias from page, in property dbname)
					foreach($this->options["colModel"] as $link_c)
					{
						if ($val['field'] == $link_c["name"] && !empty($link_c["dbname"]))
						{
							$val['field'] = $link_c["dbname"];
						}

						// fix for d/m/Y date format. strtotime expects m/d/Y
						if ($val['field'] == $link_c["name"] && $link_c["formatter"]["date"])
						{
							if ($link_c["formatoptions"]["newformat"] == "d/m/Y")
							{
								$tmp = explode("/",$val['data']);
								$val['data'] = $tmp[1]."/".$tmp[0]."/".$tmp[2];
							}
							$val['data'] = date("Y-m-d",strtotime($val['data']));
						}
					}
			
					$field = $val['field'];
					$op = $val['op'];
					$v = $val['data'];
					if(isset($v) && isset($op))
					{
						$i++;
						// ToSql in this case is absolutley needed
						$v = $this->to_sql($field,$op,$v);
			
						if ($i == 1) $qwery = " AND ";
						else $qwery .= " " .$gopr." ";
						switch ($op) {
							// in need other thing
							case 'in' :
							case 'ni' :
								$qwery .= $field.$qopers[$op]." (".$v.")";
								break;
							case 'cn' :
							case 'bw' :
								$qwery .= "LOWER($field)".$qopers[$op]." LOWER(".$v.")";
								break;
							default:
								$qwery .= $field.$qopers[$op].$v;
						}
					}
				}
			}
		}
		return $qwery;
		
	}	

	/**
	 * Advance search, make search operator sql compatible
	 */
	private function to_sql($field, $oper, $val) 
	{
		//mysql_real_escape_string is better
		if($oper=='bw' || $oper=='bn') return "'" . addslashes($val) . "%'";
		else if ($oper=='ew' || $oper=='en') return "'%" . addcslashes($val) . "'";
		else if ($oper=='cn' || $oper=='nc') return "'%" . addslashes($val) . "%'";
		else return "'" . addslashes($val) . "'";
	}
	
	/**
	 * Setter for event handler
	 */
	function set_events($arr)
	{
		$this->events = $arr;
	}

	/**
	 * Get dropdown values for select dropdowns
	 */	
	function get_dropdown_values($sql)
	{
		$str = array();
		$result = $this->execute_query($sql);

		if ($this->con)
		{
			$arr = $result->GetRows();
			
			foreach($arr as $rs)
			{
				$rs["k"] = (!empty($rs["K"])) ? $rs["K"] : $rs["k"];
				$rs["v"] = (!empty($rs["V"])) ? $rs["V"] : $rs["v"];

				$str[] = $rs["k"].":".$rs["v"];
			}
		}
		else
		{
			while($rs = mysql_fetch_array($result,MYSQL_ASSOC))
			{
				$str[] = $rs["k"].":".$rs["v"];
			}
		}
				
		$str = implode($str,";");
		return $str;
	}
	
	/**
	 * Setter for allowed actions (add/edit/del/autofilter etc)
	 */
	function set_actions($arr)
	{
		if (empty($arr))
			$arr = array();		
			
		if (empty($this->actions))
			$this->actions = array();
			
		// for add_option array
		foreach($arr as $k=>$v)
			if (is_array($v))
			{
				if (!isset($this->actions[$k]))
					$this->actions[$k] = array();
					
				$arr[$k] = array_merge($arr[$k],$this->actions[$k]);
			}		
			
		$this->actions = array_merge($this->actions,$arr);
	}
	
	/**
	 * Setter for grid customization options
	 */
	function set_options($options)
	{
		if (empty($arr))
			$arr = array();

		if (empty($this->options))
			$this->options = array();

		// for export like array merge
		foreach($options as $k=>$v)
			if (is_array($v))
			{
				if (!isset($this->options[$k]))
					$this->options[$k] = array();
					
				$options[$k] = array_merge($this->options[$k],$options[$k]);
			}
			
		$this->options = array_merge($this->options,$options);

		// enable form prev/next buttons. disabled by default now
		$show_form_nav = '';
		if ($this->options["form"]["nav"] === true)
		{
			$show_form_nav = '$("#pData").show(); $("#nData").show();';
		}
		else
		{
			$show_form_nav = '$("#pData").hide(); $("#nData").hide();';
		}

		if ($this->options["form"]["position"] == "center")
		{
			$this->options["add_options"]["beforeShowForm"] = 'function (formid) 
																{
																	'.$show_form_nav.'

																	// align dialog to center
																	var gid = formid.attr("id").replace("FrmGrid_","");
																	var dlgDiv = $("#editmod" + gid);
																	var parentDiv = dlgDiv.parent(); // div#gbox_list
																	var dlgWidth = dlgDiv.width();
																	var parentWidth = parentDiv.width();
																	var dlgHeight = dlgDiv.height();
																	var parentHeight = parentDiv.height();
																	// TODO: change parentWidth and parentHeight in case of the grid
																	//       is larger as the browser window
																	dlgDiv[0].style.top = Math.round((parentHeight-dlgHeight)/2) + "px";
																	dlgDiv[0].style.left = Math.round((parentWidth-dlgWidth)/2) + "px";	
																}';	

			$this->options["edit_options"]["beforeShowForm"] = 'function (formid) 
																{
																	'.$show_form_nav.'

																	// align dialog to center
																	var gid = formid.attr("id").replace("FrmGrid_","");
																	var dlgDiv = $("#editmod" + gid);
																	var parentDiv = dlgDiv.parent(); // div#gbox_list
																	var dlgWidth = dlgDiv.width();
																	var parentWidth = parentDiv.width();
																	var dlgHeight = dlgDiv.height();
																	var parentHeight = parentDiv.height();
																	// TODO: change parentWidth and parentHeight in case of the grid
																	//       is larger as the browser window
																	dlgDiv[0].style.top = Math.round((parentHeight-dlgHeight)/2) + "px";
																	dlgDiv[0].style.left = Math.round((parentWidth-dlgWidth)/2) + "px";																	
																}';

			$this->options["delete_options"]["beforeShowForm"] = 'function (formid) 
																{
																	'.$show_form_nav.'

																	// align dialog to center
																	var gid = formid.attr("id").replace("DelTbl_","");
																	var dlgDiv = $("#delmod" + gid);
																	var parentDiv = dlgDiv.parent(); // div#gbox_list
																	var dlgWidth = dlgDiv.width();
																	var parentWidth = parentDiv.width();
																	var dlgHeight = dlgDiv.height();
																	var parentHeight = parentDiv.height();
																	// TODO: change parentWidth and parentHeight in case of the grid
																	//       is larger as the browser window
																	dlgDiv[0].style.top = Math.round((parentHeight-dlgHeight)/2) + "px";
																	dlgDiv[0].style.left = Math.round((parentWidth-dlgWidth)/2) + "px";																	
																}';
			unset($this->options["form"]["position"]);
		}
	}

	function set_conditional_css($params)
	{
		$this->conditional_css = $params;
	}

	/**
	 * Auto generate columns for grid based on SQL / table
	 */
	function set_columns($cols = null)
	{
		if (!$this->table && !$this->select_command) die("Please specify tablename or select command");
		
		// if loading from array
		if (is_array($this->table))
		{
			$arr = $this->table;
			$f = array_keys($arr[0]);
		}
		else
		{
			// if only table is defined, make select sql for it
			if (!$this->select_command && $this->table)
				$this->select_command = "SELECT * FROM ".$this->table;

			// add where clause if not present -- fix for search feature
			if (stristr($this->select_command,"WHERE") === false)
			{
				// place group by at proper position in sql
				if (($p = stripos($this->select_command,"GROUP BY")) !== false)
				{
					$start = substr($this->select_command,0,$p);
					$end = substr($this->select_command,$p);
					$this->select_command = $start." WHERE 1=1 ".$end;
				}
				else
					$this->select_command .= " WHERE 1=1";
			}

			// make sql on single line, with no extra spaces
			$this->select_command = preg_replace("/(\r|\n)/"," ",$this->select_command);
			$this->select_command = preg_replace("/[ ]+/"," ",$this->select_command);

			// get sql column names by running nulled sql
			$sql = $this->select_command . " LIMIT 1 OFFSET 0";
			
			$sql = $this->prepare_sql($sql,$this->db_driver);
			
			$result = $this->execute_query($sql);

			if ($this->con)
			{
				$arr = $result->FetchRow();
				foreach($arr as $k=>$rs)
					$f[] = $k;
			}
			else
			{		
				$numfields = mysql_num_fields($result);
				for ($i=0; $i < $numfields; $i++) // Header
				{
					$f[] = mysql_field_name($result, $i);
				}
			}			
		}

		// if grid columns not defined, make from sql
		if (!$cols)
		{
			foreach($f as $c)
			{
				$col["title"] = ucwords(str_replace("_"," ",$c));
				$col["name"] = $c;
				$col["index"] = $c;
				$col["editable"] = true;
				$col["editoptions"] = array("size"=>20);
				$g_cols[] = $col;
			}
		}
		
		if (!$cols)
			$cols = $g_cols;
			
		// index attr is must for jqgrid, so add it in array
		for($i=0;$i<count($cols);$i++)
		{
			$cols[$i]["name"] = $cols[$i]["name"];
			$cols[$i]["index"] = $cols[$i]["name"];
			
			if (isset($cols[$i]["formatter"]) && $cols[$i]["formatter"] == "date" && empty($cols[$i]["formatoptions"]))
				$cols[$i]["formatoptions"] = array("srcformat"=>'Y-m-d',"newformat"=>'Y-m-d');

			$js_dt_fmt = $cols[$i]["formatoptions"]["newformat"];
			$js_dt_fmt = str_replace("Y", "yy", $js_dt_fmt);
			$js_dt_fmt = str_replace("m", "mm", $js_dt_fmt);
			$js_dt_fmt = str_replace("d", "dd", $js_dt_fmt);

			if (isset($cols[$i]["formatter"]) && $cols[$i]["formatter"] == "date")
			{
				$cols[$i]["editoptions"]["dataInit"] = "function(o){link_date_picker(o,'{$js_dt_fmt}');}";
				$cols[$i]["searchoptions"]["dataInit"] = "function(o){link_date_picker(o,'{$js_dt_fmt}',1);}";
			}
			
			if (isset($cols[$i]["formatter"]) && $cols[$i]["formatter"] == "datetime")
				$cols[$i]["editoptions"]["dataInit"] = "function(o){link_datetime_picker(o);}";
			
			if (isset($cols[$i]["formatter"]) && $cols[$i]["formatter"] == "wysiwyg")
			{
				$cols[$i]["formatter"] = "function(cellval,options,rowdata){return $.jgrid.htmlEncode(cellval);}";
				$cols[$i]["editoptions"]["dataInit"] = "function(o){link_editor(o);}";
			}

			if (isset($cols[$i]["formatter"]) && $cols[$i]["formatter"] == "autocomplete")
				$cols[$i]["editoptions"]["dataInit"] = "function(o){link_autocomplete(o,'".$cols[$i]["formatoptions"]["update_field"]."');}";

			// auto reload for link pattern fix
			if (!empty($cols[$i]["link"]))
			{
				$this->options["reloadedit"] = true;
				$cols[$i]["formatter"] = "link";
			}
		}

		//pr($cols);
		$this->options["colModel"] = $cols;
		foreach($cols as $c)
		{
			$this->options["colNames"][] = $c["title"];		
		}
	}

	/**
	 * Common function for db operations
	 */
	function execute_query($sql,$return="")
	{
		if ($this->con)
		{
			$ret = $this->con->Execute($sql);
			if (!$ret)
			{
				if ($this->debug)
					phpgrid_error("Couldn't execute query. ".$this->con->ErrorMsg()." - $sql");
				else
					phpgrid_error($this->error_msg);
			}

			if ($return == "insert_id")
				return $this->con->Insert_ID();
		}
		else
		{
			$ret = mysql_query($sql);
			if (!$ret)
			{
				if ($this->debug)
					phpgrid_error("Couldn't execute query. ".mysql_error()." - $sql");		
				else
					phpgrid_error($this->error_msg);
			}

			if ($return == "insert_id")
				return mysql_insert_id();
		}

		return $ret;
	}

	/**
	 * Generate JSON array for grid rendering
	 * @param $grid_id Unique ID for grid
	 */
	function render($grid_id)
	{
		// render grid for first time (non ajax), but specific grid on ajax calls
		$is_ajax = isset($_REQUEST["nd"]) || isset($_REQUEST["oper"]) || isset($_REQUEST["export"]);
		if ($is_ajax && $_REQUEST["grid_id"] != $grid_id)
			return;

		$append_by = (strpos($this->options["url"],"?") === false) ? "?" : "&";

		$this->options["url"] .= $append_by."grid_id=$grid_id";
		$this->options["editurl"] .= $append_by."grid_id=$grid_id";
		$this->options["cellurl"] .= $append_by."grid_id=$grid_id";
		
		if (isset($_REQUEST["subgrid"]))
			$grid_id .= "_".$_REQUEST["subgrid"];
			
		// generate column names, if not defined
		if (!$this->options["colNames"])
			$this->set_columns();

			
		// manage uploaded files
		foreach($this->options["colModel"] as $col)
		{
			if ($col["edittype"] == "file")
			{	
				$this->require_upload_ajax = 1;
				
				$this->options["add_options"]["onInitializeForm"] = "function(formid) 
											{
													jQuery(formid).attr('method','POST');
													jQuery(formid).attr('action','');
													jQuery(formid).attr('enctype','multipart/form-data');
																	}";
																	
				$this->options["add_options"]["recreateForm"] = true;

				$this->options["add_options"]["afterSubmit"] = "function(r,d) { 
																	ajaxFileUpload('".$col["name"]."','".$this->options["url"]."'); 
																	return [true,'','']; 
																	}";

				$this->options["edit_options"]["afterSubmit"] = "function(formid) { 
																	ajaxFileUpload('".$col["name"]."','".$this->options["url"]."'); 
																	return [true,'','']; 
																	}";
				break;
			}
		}
		// manage uploaded files
		if (count($_FILES))
		{
			$files = array_keys($_FILES);
			$fileElementName = $files[0];

			if(!empty($_FILES[$fileElementName]['error']))
			{
				switch($_FILES[$fileElementName]['error'])
				{
					case '1':
						$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
						break;
					case '2':
						$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
						break;
					case '3':
						$error = 'The uploaded file was only partially uploaded';
						break;
					case '4':
						$error = 'No file was uploaded.';
						break;

					case '6':
						$error = 'Missing a temporary folder';
						break;
					case '7':
						$error = 'Failed to write file to disk';
						break;
					case '8':
						$error = 'File upload stopped by extension';
						break;
					case '999':
					default:
						$error = 'No error code avaiable';
				}
			}
			elseif(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none')
			{
				$error = 'No file was uploaded..';
			}
			else 
			{
				foreach($this->options["colModel"] as $col)
				{
					if ($col["edittype"] == "file" && $col["name"] == $fileElementName)
					{
						$tmp_name = $_FILES[$fileElementName]["tmp_name"];
						$name = $_FILES[$fileElementName]["name"];
						
						$uploads_dir = $col["upload_dir"];
						
						if (move_uploaded_file($tmp_name, "$uploads_dir/$name"))
							$msg = "File Uploaded";
						else
							$error = "Unable to move to desired folder $uploads_dir/$name";

						break;
					}
				}
			}
			
			echo "{";
			echo				"error: '" . $error . "',\n";
			echo				"msg: '" . $msg . "'\n";
			echo "}";			
			die;
		}
		
		// for duplicate row function
		if ($_POST["id"] == "_empty")
			$_POST["oper"] = "add";

		if (isset($_POST['oper']))
		{
			$op = $_POST['oper'];
			$data = $_POST;
			$id = $data['id'];
			$pk_field = $this->options["colModel"][0]["index"];
			
			// reformat date w.r.t mysql
			foreach( $this->options["colModel"] as $c )
			{
				// fix for d/m/Y date format. strtotime expects m/d/Y
				if ($c["formatoptions"]["newformat"] == "d/m/Y")
				{
					$tmp = explode("/",$data[$c["index"]]);
					$data[$c["index"]] = $tmp[1]."/".$tmp[0]."/".$tmp[2];
				}

				// put zeros for blank date field
				if (($c["formatter"] == "date" || $c["formatter"] == "datetime") && (empty($data[$c["index"]]) || $data[$c["index"]] == "//"))
				{
					$data[$c["index"]] = "NULL";
				}
				// if db field allows null, then set NULL
				else if ($c["isnull"] && empty($data[$c["index"]]))
				{
					$data[$c["index"]] = "NULL";
				}
				else if ($c["formatter"] == "date")
				{
					$data[$c["index"]] = date("Y-m-d",strtotime($data[$c["index"]]));
				}
				else if ($c["formatter"] == "datetime")
				{
					$data[$c["index"]] = date("Y-m-d H:i:s",strtotime($data[$c["index"]]));
				}
				else if ($c["formatter"] == "autocomplete")
				{
					unset($data[$c["index"]]);
				}					
			}

			// handle grid operations of CRUD
			switch($op)
			{
				case "autocomplete":
					$field = $data['element'];
					$term = $data['term'];
					foreach( $this->options["colModel"] as $c )
					{
						if ($c["index"] == $field)
						{
							$sql = $c["formatoptions"]["sql"]. " WHERE {$c["formatoptions"]["search_on"]} like '$term%'";
							$rs = $this->execute_query($sql);
							$data_client = array();
							while($row = mysql_fetch_assoc($rs)) 
							{
							    $arr['id'] = $row['client_id'];
							    $arr['label'] = $row['name'];
							    $arr['value'] = $row['name'];
							    $data_arr[] = $arr;
							}
							header('Content-type: application/json');
							echo json_encode($data_arr);
							die;
						}
					}
					break;

				case "add":
					unset($data['id']);
					unset($data['oper']);
					
					$update_str = array();

					// custom onupdate event execution
					if (!empty($this->events["on_insert"]))
					{
						$func = $this->events["on_insert"][0];
						$obj = $this->events["on_insert"][1];
						$continue = $this->events["on_insert"][2];
						
						if ($obj)
							call_user_method($func,$obj,array($pk_field => $id, "params" => &$data));
						else
							call_user_func($func,array($pk_field => $id, "params" => &$data));
						
						if (!$continue)
							break;
					}
					
					foreach($data as $k=>$v)
					{
						// remove any table alias from query - obseleted
						if (strstr($k,"::") !== false)
							list($tmp,$k) = explode("::",$k);
						$k = addslashes($k);
						$v = addslashes($v);
						$fields_str[] = "$k";

						$v = ($v == "NULL") ? $v : "'$v'";
						$values_str[] = "$v";
					}
					
					$insert_str = "(".implode(",",$fields_str).") VALUES (".implode(",",$values_str).")";
					
					$sql = "INSERT INTO {$this->table} $insert_str";

					$insert_id = $this->execute_query($sql,"insert_id");

					// custom onupdate event execution
					if (!empty($this->events["on_after_insert"]))
					{
						$func = $this->events["on_after_insert"][0];
						$obj = $this->events["on_after_insert"][1];
						$continue = $this->events["on_after_insert"][2];
						
						if ($obj)
							call_user_method($func,$obj,array($pk_field => $insert_id, "params" => &$data));
						else
							call_user_func($func,array($pk_field => $insert_id, "params" => &$data));
						
						if (!$continue)
							break;
					}
					
					// for inline row addition, return insert id to update PK of grid (e.g. order_id#33)
					if ($id == "new_row")
						die($pk_field."#".$insert_id);
					break;
					
				case "edit":
					//pr($_POST);
					unset($data['id']);
					unset($data['oper']);
					
					$update_str = array();

					// custom onupdate event execution
					if (!empty($this->events["on_update"]))
					{
						$func = $this->events["on_update"][0];
						$obj = $this->events["on_update"][1];
						$continue = $this->events["on_update"][2];
						
						if ($obj)
							call_user_method($func,$obj,array($pk_field => $id, "params" => &$data));
						else
							call_user_func($func,array($pk_field => $id, "params" => &$data));
						
						if (!$continue)
							break;
					}

					foreach($data as $k=>$v)
					{
						// remove any table alias from query - obseleted
						if (strstr($k,"::") !== false)
							list($tmp,$k) = explode("::",$k);
							
						$k = addslashes($k);
						$v = addslashes($v);

						$v = ($v == "NULL") ? $v : "'$v'";
						$update_str[] = "$k=$v";
					}
					
					$update_str = "SET ".implode(",",$update_str);
					
					if (strstr($pk_field,"::") !== false)
					{
						$pk_field = explode("::",$pk_field);
						$pk_field = $pk_field[1];
					}

					$sql = "UPDATE {$this->table} $update_str WHERE $pk_field = '$id'";
					$this->execute_query($sql);
				break;			
				
				case "del":
					
					// obseleted
					if (strstr($pk_field,"::") !== false)
					{
						$pk_field = explode("::",$pk_field);
						$pk_field = $pk_field[1];
					}
					
					// custom onupdate event execution
					if (!empty($this->events["on_delete"]))
					{
						$func = $this->events["on_delete"][0];
						$obj = $this->events["on_delete"][1];
						$continue = $this->events["on_delete"][2];
						if ($obj)
							call_user_method($func,$obj,array($pk_field => $id));
						else
							call_user_func($func,array($pk_field => $id));
						
						if (!$continue)
							break;
					}
					
					$id = "'".implode("','",explode(",",$id))."'";
					$sql = "DELETE FROM {$this->table} WHERE $pk_field IN ($id)";

					$this->execute_query($sql);
				break;
			}
			
			die;
		}
		
		// apply search conditions (where clause)
		$wh = "";
		
		if (!isset($_REQUEST['_search']))
			$_REQUEST['_search'] = "";
		
		$searchOn = $this->strip($_REQUEST['_search']);
		if($searchOn=='true') 
		{
			$fld = $this->strip($_REQUEST['searchField']);
			
			$cols = array();
			foreach($this->options["colModel"] as $col)
				$cols[] = $col["index"];

			// quick search bar
			if (!$fld)
			{
				$searchstr = $this->strip($_REQUEST['filters']);
				$wh = $this->construct_where($searchstr);
			}
			// search popup form, simple one -- not used anymore
			else
			{
				if(in_array($fld,$cols)) 
				{	
					$fldata = $this->strip($_REQUEST['searchString']);
					$foper = $this->strip($_REQUEST['searchOper']);
					// costruct where
					$wh .= " AND ".$fld;
					switch ($foper) {					
						case "eq":
							if(is_numeric($fldata)) {
								$wh .= " = ".$fldata;
							} else {
								$wh .= " = '".$fldata."'";
							}
							break;
						case "ne":
							if(is_numeric($fldata)) {
								$wh .= " <> ".$fldata;
							} else {
								$wh .= " <> '".$fldata."'";
							}
							break;
						case "lt":
							if(is_numeric($fldata)) {
								$wh .= " < ".$fldata;
							} else {
								$wh .= " < '".$fldata."'";
							}
							break;
						case "le":
							if(is_numeric($fldata)) {
								$wh .= " <= ".$fldata;
							} else {
								$wh .= " <= '".$fldata."'";
							}
							break;
						case "gt":
							if(is_numeric($fldata)) {
								$wh .= " > ".$fldata;
							} else {
								$wh .= " > '".$fldata."'";
							}
							break;
						case "ge":
							if(is_numeric($fldata)) {
								$wh .= " >= ".$fldata;
							} else {
								$wh .= " >= '".$fldata."'";
							}
							break;
						case "ew":
							$wh .= " LIKE '%".$fldata."'";
							break;
						case "en":
							$wh .= " NOT LIKE '%".$fldata."'";
							break;
						case "cn":
							$wh .= " LIKE '%".$fldata."%'";
							break;
						case "nc":
							$wh .= " NOT LIKE '%".$fldata."%'";
							break;
						case "in":
							$wh .= " IN (".$fldata.")";
							break;
						case "ni":
							$wh .= " NOT IN (".$fldata.")";
							break;
						case "bw":
						default:
							$fldata .= "%";
							$wh .= " LIKE '".$fldata."'";
							break;
					}
				}
			}
			// setting to persist where clause in export option
			$_SESSION["jqgrid_filter"] = $wh;
		}
		elseif($searchOn=='false') 
		{
			$_SESSION["jqgrid_filter"] = '';
		}
		
		// generate main json
		if (isset($_GET['jqgrid_page']))
		{
			$page = $_GET['jqgrid_page']; // get the requested page
			$limit = $_GET['rows']; // get how many rows we want to have into the grid
			$sidx = $_GET['sidx']; // get index row - i.e. user click to sort
			$sord = $_GET['sord']; // get the direction

			if(!$sidx) $sidx = 1;
			if(!$limit) $limit = 20;

			$sidx = str_replace("::",".",$sidx);
			
			// if export option is requested
			if (isset($_GET["export"]))
			{
				$arr = array();

				// by default export all
				$export_where = "";
				if ($this->options["export"]["range"] == "filtered")
					$export_where = $_SESSION["jqgrid_filter"];

				$limit_sql= "";
				if ($this->options["export"]["paged"] == "1")
				{
					$offset = $limit*$page - $limit; // do not put $limit*($page - 1)
					if ($offset<0) $offset = 0;
					$limit_sql = "LIMIT $limit OFFSET $offset";
				}
				
				if (($p = stripos($this->select_command,"GROUP BY")) !== false)
				{
					$start = substr($this->select_command,0,$p);
					$end = substr($this->select_command,$p);
					$SQL = $start.$export_where.$end." ORDER BY $sidx $sord $limit_sql";
				}
				else
					$SQL = $this->select_command.$export_where." ORDER BY $sidx $sord $limit_sql";

				$result = $this->execute_query($SQL);

				// export only selected columns
				$cols_not_to_export = array();
				if ($this->options["colModel"])
				{
					foreach ($this->options["colModel"] as $c)
						if ($c["export"] === false)
							$cols_not_to_export[] = $c["name"];
				}
				
				foreach ($this->options["colModel"] as $c)
					$header[$c["name"]] = $c["title"];
				$arr[] = $header;
				
				if ($this->con)
					$arr = $result->GetRows();
				else
				{
					while($row = mysql_fetch_array($result,MYSQL_ASSOC))
					{
						foreach($header as $k=>$v)
							$export_data[$k] = $row[$k];
							
						$arr[] = $export_data;
					}
				}

				if (!empty($cols_not_to_export))
				{
					$export_arr = array();
					foreach($arr as $arr_item)
					{
						foreach($arr_item as $k=>$i)
						{						
							if (in_array($k, $cols_not_to_export))
							{
								unset($arr_item[$k]);
							}
						}
						$export_arr[] = $arr_item;
					}	
					$arr = $export_arr;
				}
				
				if (!$this->options["export"]["filename"])
					$this->options["export"]["filename"] = $grid_id;
					
				if (!$this->options["export"]["sheetname"])
					$this->options["export"]["sheetname"] = ucwords($grid_id). " Sheet";
					
				if ($this->options["export"]["format"] == "pdf")
				{
					$html = "";
					
					// if customized pdf render is defined, use that
					if (!empty($this->events["on_render_pdf"]))
					{	
						$func = $this->events["on_render_pdf"][0];
						$obj = $this->events["on_render_pdf"][1];
						if ($obj)
							$html = call_user_method($func,$obj,array("grid" => $this, "data" => $arr));
						else
							$html = call_user_func($func,array("grid" => $this, "data" => $arr));
					}
					else
					{
						$html .= "<h1>".$this->options["export"]["heading"]."</h1>";
						$html .= '<table border="1" cellpadding="4" cellspacing="0">';
						
						$i = 0;
						foreach($arr as $v)
						{
							$shade = ($i++ % 2) ? 'bgcolor="#efefef"' : '';
							$html .= "<tr>";
							foreach($v as $d)
							{
								// bold header
								if  ($i == 1)
									$html .= "<td bgcolor=\"lightgrey\"><strong>$d</strong></td>";
								else
									$html .= "<td $shade>$d</td>";
							}
							$html .= "</tr>";
						}
						$html .= "</table>";
					}

					$orientation = $this->options["export"]["orientation"];
					if ($orientation == "landscape")
						$orientation = "L";
					else
						$orientation = "P";

					$paper = $this->options["export"]["paper"];

					// Using opensource TCPdf lib
					// for more options visit http://www.tcpdf.org/examples.php

					require_once('tcpdf/config/lang/eng.php');
					require_once('tcpdf/tcpdf.php');

					// create new PDF document
					$pdf = new TCPDF($orientation, PDF_UNIT, $paper, true, 'UTF-8', false);

					// set document information
					$pdf->SetCreator("www.phpgrid.org");
					$pdf->SetAuthor('www.phpgrid.org');
					$pdf->SetTitle('TCPDF Example 002');
					$pdf->SetSubject($this->options["caption"]);
					$pdf->SetKeywords('www.phpgrid.org');

					// remove default header/footer
					$pdf->setPrintHeader(false);
					$pdf->setPrintFooter(false);

					// set default monospaced font
					$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
					$pdf->setFontSubsetting(false);

					//set margins
					$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

					//set auto page breaks
					$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

					//set image scale factor
					$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

					// set some language dependent data:

					// lines for rtl pdf generation
					if ($this->options["direction"] == "rtl")
					{
						$lg = Array();
						$lg['a_meta_charset'] = 'UTF-8';
						$lg['a_meta_dir'] = 'rtl';
						$lg['a_meta_language'] = 'fa';
						$lg['w_page'] = 'page';
					}
					$pdf->setLanguageArray($lg);

					// To set your custom font
					// $fontname = $pdf->addTTFfont('/path-to-font/DejaVuSans.ttf', 'TrueTypeUnicode', '', 32);

					// set font http://www.tcexam.org/doc/code/classTCPDF.html#afd56e360c43553830d543323e81bc045
					$pdf->SetFont('helvetica', '', 12);

					// add a page
					$pdf->AddPage();

					// output the HTML content
					$pdf->writeHTML($html, true, false, true, false, '');

					//Close and output PDF document
					$pdf->Output($this->options["export"]["filename"].".pdf", 'I');
					die;
				}
				else if ($this->options["export"]["format"] == "csv")
				{
		            header( 'Content-Type: text/csv' );
		            header( 'Content-Disposition: attachment;filename='.$this->options["export"]["filename"].'.csv');		
		            $fp = fopen('php://output', 'w');
		            foreach ($arr as $key => $value) 
		            {
		            	fputcsv($fp, $value);
		            }
		            die;			
				}
				else
				{
					$html = "";
					$html .= "<table border='0' cellpadding='2' cellspacing='2'>";
					$i = 0;
					foreach($arr as $v)
					{
						$html .= "<tr>";
						foreach($v as $d)
							$html .= "<td>$d</td>";
						$html .= "</tr>";
					}
					$html .= "<table>";

					// Convert to UTF-16LE
					$output = mb_convert_encoding($html, 'UTF-16LE', 'UTF-8'); 

					// Prepend BOM
					$output = "\xFF\xFE" . $output;

					header('Pragma: public');
					header("Content-type: application/x-msexcel"); 
					header('Content-Disposition: attachment;  filename="'.$this->options["export"]["filename"].'.xls"');

					echo $output;	
				}
				die;
			}
			
			// make count query
			if (($p = stripos($this->select_command,"GROUP BY")) !== false)
			{
				$sql_count = preg_replace("/SELECT (.*) FROM/i","SELECT 1 as c FROM",$this->select_command);
				$p = stripos($sql_count,"GROUP BY");
				$start_q = substr($sql_count,0,$p);
				$end_q = substr($sql_count,$p);
				$sql_count = "SELECT count(*) as c FROM ($start_q $wh $end_q) o";
			}
			else
			{
				$sql_count = $this->select_command.$wh;
				$sql_count = "SELECT count(*) as c FROM (".$sql_count.") table_count";
			}
			# print_r($sql_count);
			$result = $this->execute_query($sql_count);

			if ($this->con)
			{
				$row = $result->FetchRow();
			}
			else
			{
				$row = mysql_fetch_array($result,MYSQL_ASSOC);
			}

			$count = $row['c'];
			
			// fix for oracle, alias in capitals
			if (empty($count)) 
				$count = $row['C'];

			if( $count > 0 ) {
				$total_pages = ceil($count/$limit);
			} else {
				$total_pages = 0;
			}

			if ($page > $total_pages) $page=$total_pages;
			$start = $limit*$page - $limit; // do not put $limit*($page - 1)
			if ($start<0) $start = 0;
	
			$responce = new stdClass();
			$responce->page = $page;
			$responce->total = $total_pages;
			$responce->records = $count;

			if (($p = stripos($this->select_command,"GROUP BY")) !== false)
			{
				$start_q = substr($this->select_command,0,$p);
				$end_q = substr($this->select_command,$p);
				$SQL = "$start_q $wh $end_q ORDER BY $sidx $sord LIMIT $limit OFFSET $start";
			}
			else
			{
				$SQL = $this->select_command.$wh." ORDER BY $sidx $sord LIMIT $limit OFFSET $start";
			}

			$SQL = $this->prepare_sql($SQL,$this->db_driver);
			
			$result = $this->execute_query($SQL);

			if ($this->con)
			{
				$rows = $result->GetRows();
				
				// simulate artificial paging for mssql
				if (count($rows) > $limit)
					$rows = array_slice($rows,count($rows) - $limit);
			}
			else
			{
				$rows = array();
				while($row = mysql_fetch_array($result,MYSQL_ASSOC))
					$rows[] = $row;
			}

			// custom on_data_display event execution
			if (!empty($this->events["on_data_display"]))
			{
				$func = $this->events["on_data_display"][0];
				$obj = $this->events["on_data_display"][1];
				$continue = $this->events["on_data_display"][2];
				
				if ($obj)
					call_user_method($func,$obj,array("params" => &$rows));
				else
					call_user_func($func,array("params" => &$rows));
				
				if (!$continue)
					break;
			}
			
			foreach ($rows as $row)	
			{
				// apply php level formatter for image url 30.12.10
				foreach($this->options["colModel"] as $c)
				{
					$col_name = $c["name"];
					
					if (isset($c["default"]) && !isset($row[$col_name]))
						$row[$col_name] = $c["default"];

					// link data in grid to any given url
					if (!empty($c["default"]))
					{
						// replace any param in link e.g. http://domain.com?id={id} given that, there is a $col["name"] = "id" exist
						$row[$col_name] = $this->replace_row_data($row,$c["default"]);
					}
					
					// check conditional data
					if (!empty($c["condition"][0]))
					{
						$r = true;

						// replace {} placeholders from connditional data
						$c["condition"][1] = $this->replace_row_data($row,$c["condition"][1]);
						$c["condition"][2] = $this->replace_row_data($row,$c["condition"][2]);

						eval("\$r = ".$c["condition"][0].";");
						$row[$col_name] = ( $r ? $c["condition"][1] : $c["condition"][2]);
					}

					// link data in grid to any given url
					if (!empty($c["link"]))
					{
						// replace any param in link e.g. http://domain.com?id={id} given that, there is a $col["name"] = "id" exist
						foreach($this->options["colModel"] as $link_c)
						{
							$link_row_data = urlencode($row[$link_c["name"]]);
							$c["link"] = str_replace("{".$link_c["name"]."}", $link_row_data, $c["link"]);
						}
						
						if (!empty($c["linkoptions"]))
							$attr = $c["linkoptions"];

						$row[$col_name] = "<a $attr href='{$c["link"]}'>{$row[$col_name]}</a>";
					}

					// render row data as "src" value of <img> tag
					if (isset($c["formatter"]) && $c["formatter"] == "image")
					{
						$attr = array();
						foreach($c["formatoptions"] as $k=>$v)
							$attr[] = "$k='$v'";
						
						$attr = implode(" ",$attr);
						$row[$col_name] = "<img $attr src='".$row[$col_name] ."'>";
					}
						
					// show masked data in password
					if (isset($c["formatter"]) && $c["formatter"] == "password")
						$row[$col_name] = "*****";			
				}

				foreach($row as $k=>$r)
					$row[$k] = stripslashes($row[$k]);

				$responce->rows[] = $row;
			}
			
			echo json_encode($responce);
			die;
		}		
		
		// if loading from array
		if (is_array($this->table))
		{
			$this->options["data"] = json_encode($this->table);
			$this->options["datatype"] = "local";	
			$this->actions["rowactions"] = false;		
			$this->actions["add"] = false;		
			$this->actions["edit"] = false;		
			$this->actions["delete"] = false;		
		}

		// few overides - pagination fixes
		$this->options["pager"] = '#'.$grid_id."_pager";
		$this->options["jsonReader"] = array("repeatitems" => false, "id" => "0");

		// allow/disallow edit,del operations
		if ( ($this->actions["edit"] === false && $this->actions["delete"] === false) || $this->options["cellEdit"] === true)
			$this->actions["rowactions"] = false;
			
		if ($this->actions["rowactions"] !== false)
		{
			// CRUD operation column
			$f = false;
			$defined = false;
			foreach($this->options["colModel"] as &$c)
			{
				if ($c["name"] == "act")
				{
					$defined = &$c;
				}
					
				if (!empty($c["width"]))
				{
					$f = true;
				}
			}
			
			// width adjustment for row actions column
			if ($f)
				$action_column = array("name"=>"act", "align"=>"center", "index"=>"act", "width"=>"40", "sortable"=>false, "search"=>false, "viewable"=>false);
			else
				$action_column = array("name"=>"act", "align"=>"center", "index"=>"act", "width"=>"60", "sortable"=>false, "search"=>false, "viewable"=>false);

			if (!$defined)
			{
				$this->options["colNames"][] = "Actions";
				$this->options["colModel"][] = $action_column;
			}
			else
				$defined = array_merge($action_column,$defined);
		}		

		$out = json_encode_jsfunc($this->options);
		$out = substr($out,0,strlen($out)-1);

		// create Edit/Delete - Save/Cancel column in grid
		if ($this->actions["rowactions"] !== false)
		{
			$act_links = array();
			if ($this->actions["edit"] !== false)
				$act_links[] = "<a title=\"Edit this row\" href=\"javascript:void(0);\" onclick=\"jQuery(\'#$grid_id\').editRow(\''+cl+'\',true); jQuery(this).parent().hide(); jQuery(this).parent().next().show(); \">Edit</a>";

			if ($this->actions["delete"] !== false)
				$act_links[] = "<a title=\"Delete this row\" href=\"javascript:void(0);\" onclick=\"jQuery(\'#$grid_id\').delGridRow(\''+cl+'\'); \">Delete</a>";

			if ($this->actions["clone"] === true)
				$act_links[] = "<a title=\"Duplicate this row\" href=\"javascript:void(0);\" onclick=\"duplicate_row(\'#$grid_id\',\''+cl+'\'); \">Clone</a>";

			$act_links = implode(" | ", $act_links);

			$out .= ",'gridComplete': function(){
						var ids = jQuery('#$grid_id').jqGrid('getDataIDs');
						for(var i=0;i < ids.length;i++){
							var cl = ids[i];
							
							be = '$act_links'; 
							
							se = ' <a title=\"Save this row\" href=\"javascript:void(0);\" onclick=\"if (jQuery(\'#$grid_id\').saveRow(\''+cl+'\')) { jQuery(this).parent().hide(); jQuery(this).parent().prev().show(); }\">Save</a>'; 
							ce = ' | <a title=\"Restore this row\" href=\"javascript:void(0);\" onclick=\"jQuery(\'#$grid_id\').restoreRow(\''+cl+'\'); jQuery(this).parent().hide(); jQuery(this).parent().prev().show();\">Cancel</a>'; 
							
							if (ids[i] == 'new_row')
							{
								se = ' <a title=\"Save this row\" href=\"javascript:void(0);\" onclick=\"jQuery(\'#{$grid_id}_ilsave\').click(); \">Save</a>'; 
								ce = ' | <a title=\"Restore this row\" href=\"javascript:void(0);\" onclick=\"jQuery(\'#{$grid_id}_ilcancel\').click(); jQuery(this).parent().hide(); jQuery(this).parent().prev().show();\">Cancel</a>'; 
								jQuery('#$grid_id').jqGrid('setRowData',ids[i],{act:'<span style=display:none id=\"edit_row_{$grid_id}_'+cl+'\">'+be+'</span>'+'<span id=\"save_row_{$grid_id}_'+cl+'\">'+se+ce+'</span>'});
							}
							else
								jQuery('#$grid_id').jqGrid('setRowData',ids[i],{act:'<span id=\"edit_row_{$grid_id}_'+cl+'\">'+be+'</span>'+'<span style=display:none id=\"save_row_{$grid_id}_'+cl+'\">'+se+ce+'</span>'});
						}	
					}";

			/*
			// theme buttons -- not looking good
			$out .= ",'gridComplete': function(){
						var ids = jQuery('#$grid_id').jqGrid('getDataIDs');
						for(var i=0;i < ids.length;i++){
							var cl = ids[i];
							be = ' <a style=\"padding:0 0.5em;padding-left:1.6em;font-weight:normal;\" class=\"fm-button fm-button-icon-left ui-state-default ui-corner-all\" title=\"Edit this row\" onclick=\"jQuery(\'#$grid_id\').editRow('+cl+',true); jQuery(this).parent().hide(); jQuery(this).parent().next().show(); \">Edit <span class=\"ui-icon ui-icon-pencil\"></span></a>'; 
							de = ' <a style=\"padding:0 0.5em;padding-left:1.6em;font-weight:normal;\" class=\"fm-button fm-button-icon-left ui-state-default ui-corner-all\" title=\"Delete this row\" onclick=\"jQuery(\'#$grid_id\').delRowData('+cl+'); \">Delete <span class=\"ui-icon ui-icon-close\"></span></a>';

							se = ' <a style=\"padding:0 0.5em;padding-left:1.6em;font-weight:normal;\" class=\"fm-button fm-button-icon-left ui-state-default ui-corner-all\" title=\"Save this row\" onclick=\"jQuery(\'#$grid_id\').saveRow('+cl+'); jQuery(this).parent().hide(); jQuery(this).parent().prev().show();\">Save <span class=\"ui-icon ui-icon-disk\"></span></a>'; 
							ce = ' <a style=\"padding:0 0.5em;padding-left:1.6em;font-weight:normal;\" class=\"fm-button fm-button-icon-left ui-state-default ui-corner-all\" title=\"Restore this row\" href=\"javascript:void(0);\" onclick=\"jQuery(\'#$grid_id\').restoreRow('+cl+'); jQuery(this).parent().hide(); jQuery(this).parent().prev().show();\">Cancel <span class=\"ui-icon ui-icon-cancel\"></span></a>'; 
							
							jQuery('#$grid_id').jqGrid('setRowData',ids[i],{act:'<div style=\"white-space:nowrap;float:left\" id=\"edit_row_'+cl+'\">'+be+de+'</div>'+'<div style=\"white-space:nowrap;float:left;display:none;\" id=\"save_row_'+cl+'\">'+se+ce+'</div>'});
						}	
					}";
			*/
		}					
		
		// double click editing option
		if ($this->actions["rowactions"] !== false && $this->actions["edit"] !== false && $this->options["cellEdit"] !== true)
		{
			if ($this->options["reloadedit"] === true)
				$reload_after_edit = "jQuery('#$grid_id').jqGrid().trigger('reloadGrid',[{jqgrid_page:1}]);";

			$out .= ",'ondblClickRow':function(id)
						{
							if(id && id!==lastSel){ 
								jQuery('#$grid_id').restoreRow(lastSel); 
								
								// disabled previously edit icons
								jQuery('#edit_row_{$grid_id}_'+lastSel).show();
								jQuery('#save_row_{$grid_id}_'+lastSel).hide();								
								
								lastSel=id; 								
							}
							
							jQuery('#$grid_id').editRow(id, true, function(){}, function(){
																					jQuery('#edit_row_{$grid_id}_'+id).show();
																					jQuery('#save_row_{$grid_id}_'+id).hide();
																					return true;
																				},null,null,function(){
																				
																				// force reload grid after inline save
																				$reload_after_edit
																				
																				},null,
																				function(){
																					jQuery('#edit_row_{$grid_id}_'+id).show();
																					jQuery('#save_row_{$grid_id}_'+id).hide();
																					return true;
																				}
														); 
							
							jQuery('#edit_row_{$grid_id}_'+id).hide();
							jQuery('#save_row_{$grid_id}_'+id).show();
						}";
		}
		
		// if subgrid is there, enable subgrid feature
		if (isset($this->options["subgridurl"]) && $this->options["subgridurl"] != '') 
		{
			// we pass two parameters
			// subgrid_id is a id of the div tag created within a table
			// the row_id is the id of the row
			// If we want to pass additional parameters to the url we can use
			// the method getRowData(row_id) - which returns associative array in type name-value
			// here we can easy construct the following
					
			$pass_params = "false";
			if (!empty($this->options["subgridparams"]))
				$pass_params = "true";
				
			$out .= ",'subGridRowExpanded': function(subgridid, id) 
											{ 
												var data = {subgrid:subgridid, rowid:id};
												
												if('$pass_params' == 'true') {
													var anm= '".$this->options["subgridparams"]."';
													anm = anm.split(',');
													var rd = jQuery('#".$grid_id."').jqGrid('getRowData', id);
													if(rd) {
														for(var i=0; i<anm.length; i++) {
															if(rd[anm[i]]) {
																data[anm[i]] = rd[anm[i]];
															}
														}
													}
												}
												jQuery('#'+jQuery.jgrid.jqID(subgridid)).load('".$this->options["subgridurl"]."',data);
											}";				
		}

		// on error
		$out .= ",'loadError': function(xhr,status, err) { 
					try 
					{
						jQuery.jgrid.info_dialog(jQuery.jgrid.errors.errcap,'<div class=\"ui-state-error\">'+ xhr.responseText +'</div>', 
													jQuery.jgrid.edit.bClose,{buttonalign:'right'});
					} 
					catch(e) { alert(xhr.responseText);}
				}
				";

		// on row selection operation
		$out .= ",'onSelectRow': function(ids) { ";
				if (isset($this->options["detail_grid_id"]) && $this->options["detail_grid_id"] != '') 
				{
					$detail_grid_id	= $this->options["detail_grid_id"];
					$d_grids = explode(",", $detail_grid_id);

					foreach($d_grids as $detail_grid_id) 
					{
						$detail_url= "?grid_id=". $detail_grid_id;

						$out .= "
		
						var data = '';
						if ('{$this->options["subgridparams"]}'.length > 0)
						{
							var anm = '".$this->options["subgridparams"]."';
							anm = anm.split(',');
							var rd = jQuery('#".$grid_id."').jqGrid('getRowData', ids);
							if(rd) {
								for(var i=0; i<anm.length; i++) {
									if(rd[anm[i]]) {
										data += '&' + anm[i] + '=' + rd[anm[i]];
									}
								}
							}
						}	
							
						if(ids == null) 
						{
							ids=0;
							if(jQuery('#".$detail_grid_id."').jqGrid('getGridParam','records') >0 )
							{
								jQuery('#".$detail_grid_id."').jqGrid('setGridParam',{url:'".$detail_url."&rowid='+ids+data,editurl:'".$detail_url."&rowid='+ids,jqgrid_page:1});
								jQuery('#".$detail_grid_id."').trigger('reloadGrid',[{jqgrid_page:1}]);
							}
						} 
						else 
						{
							jQuery('#".$detail_grid_id."').jqGrid('setGridParam',{url:'".$detail_url."&rowid='+ids+data,editurl:'".$detail_url."&rowid='+ids,jqgrid_page:1});
							jQuery('#".$detail_grid_id."').trigger('reloadGrid',[{jqgrid_page:1}]);			
						}
						";
					}
				};

				if (!empty($this->events["js_on_select_row"])) 
				{
					$out .= "if (typeof({$this->events["js_on_select_row"]}) != 'undefined') {$this->events["js_on_select_row"]}(ids);";
				}	
		// closing of select row events
		$out .= "}";

		// on load complete operation
		$out .= ",'loadComplete': function(ids) { ";
		$out .= "if(ids.rows) jQuery.each(ids.rows,function(i){";

				if (count($this->conditional_css))
				{
					foreach ($this->conditional_css as $value) 
					{
						if ($value["op"] == "cn")
						{
							$out .= "
								if (this.{$value[column]}.toLowerCase().indexOf('{$value[value]}'.toLowerCase()) != -1)
							 	{
							 		jQuery('tr.jqgrow:eq('+i+')').removeClass('ui-widget-content').css({{$value[css]}});
							 	}";
						}
						else if ($value["op"] == "eq")
						{
							$out .= "
								if (this.{$value[column]}.toLowerCase() == '{$value[value]}'.toLowerCase())
							 	{
							 		jQuery('tr.jqgrow:eq('+i+')').removeClass('ui-widget-content').css({{$value[css]}});
							 	}";
						}
						else if ($value["op"] == "<" || $value["op"] == "<=" || $value["op"] == ">" || $value["op"] == ">=" || $value["op"] == "!=")
						{
							$out .= "
								if (this.{$value[column]} {$value["op"]} {$value[value]})
							 	{
							 		jQuery('tr.jqgrow:eq('+i+')').removeClass('ui-widget-content').css({{$value[css]}});
							 	}";
						}
						// column formatting
						else if (empty($value["op"]) && !empty($value["column"]) && !empty($value["css"]))
						{
							$out .= "
							 	{
							 		jQuery('td[aria-describedby={$grid_id}_{$value["column"]}]').removeClass('ui-widget-content').css({{$value[css]}});
							 	}";							
						}
					}
				}
		$out .= "});";

		// closing of load complete events
		$out .= "}";
		
		// closing of param list
		$out .= "}";
		
		// Geneate HTML/JS code
		ob_start();
		?>
			<table id="<?php echo $grid_id?>"></table> 
			<div id="<?php echo $grid_id."_pager"?>"></div> 

			<script>
			var phpgrid = jQuery("#<?php echo $grid_id?>");
			var phpgrid_pager = jQuery("#<?php echo $grid_id."_pager"?>");
			jQuery(document).ready(function(){
				<?php echo $this->render_js($grid_id,$out);?>
			});	
			</script>	
		<?php
		return ob_get_clean();
	}
	
	/**
	 * JS code related to grid rendering
	 */
	function render_js($grid_id,$out)
	{
	?>
		var lastSel;
		duplicate_row = function (table, Id) 
		{
		   	newId = null;
		   	myData = jQuery(table).getRowData(Id)
		   	jQuery.ui.datepicker = null;
		   	jQuery(table).jqGrid('addRow', {
											    rowID : "_empty",
											    initdata : myData,
											    position :"first",
											    useDefValues : false,
											    useFormatter : false,
											    addRowParams : {extraparam:{}}
											});
			jQuery(table).jqGrid('saveRow',"_empty");
			jQuery(table).jqGrid().trigger('reloadGrid',[{jqgrid_page:1}]);
		}

		var grid_<?php echo $grid_id?> = jQuery("#<?php echo $grid_id?>").jqGrid( jQuery.extend(<?php echo $out?>, (typeof(opts) == 'undefined') ? {} : opts ) );
		
		jQuery("#<?php echo $grid_id?>").jqGrid('navGrid','#<?php echo $grid_id."_pager"?>',
				{
					edit: <?php echo ($this->actions["edit"] === false)?"false":"true"?>,
					add: <?php echo ($this->actions["add"] === false)?"false":"true"?>,
					del: <?php echo ($this->actions["delete"] === false)?"false":"true"?>,
					view: <?php echo ($this->actions["view"] === false)?"false":"true"?>
				},
				<?php echo json_encode_jsfunc($this->options["edit_options"])?>,
				<?php echo json_encode_jsfunc($this->options["add_options"])?>,
				<?php echo json_encode_jsfunc($this->options["delete_options"])?>,
				{
					multipleSearch:<?php echo ($this->actions["search"] == "advance")?"true":"false"?>, 
					sopt:['eq','ne','lt','le','gt','ge','bw','bn','in','ni','ew','en','cn','nc','nu','nn']
				}
				);
		
				
		<?php if ($this->actions["inlineadd"] !== false) { ?>
		jQuery('#<?php echo $grid_id?>').jqGrid('inlineNav','#<?php echo $grid_id."_pager"?>',{"addtext":"Inline","edit":false,"save":true,"cancel":true,
		"addParams":{"aftersavefunc":function (id, res)
		{
			// set returned pk in new row of grid
			res = res.responseText.split("#");
			try {
				$(this).jqGrid('setCell', id, res[0], res[1]);
				$("#"+id, "#"+this.p.id).removeClass("jqgrid-new-row").attr("id",res[1] );
			} catch (asr) {}
			
			// but reload grid, to work properly
			jQuery('#<?php echo $grid_id?>').trigger("reloadGrid",[{jqgrid_page:1}]);
		}},"editParams":{"aftersavefunc":function (id, res)
		{
			// set returned pk in new row of grid
			res = res.responseText.split("#");
			try {
				$(this).jqGrid('setCell', id, res[0], res[1]);
				$("#"+id, "#"+this.p.id).removeClass("jqgrid-new-row").attr("id",res[1] );
			} catch (asr) {}

			// but reload grid, to work properly			
			jQuery('#<?php echo $grid_id?>').trigger("reloadGrid",[{jqgrid_page:1}]);
		}}});
		<?php } ?>
			
		<?php if ($this->actions["autofilter"] !== false) { ?>
		// auto filter with contains search
		jQuery("#<?php echo $grid_id?>").jqGrid('filterToolbar',{stringResult: true,searchOnEnter : false, defaultSearch:'cn'}); 
		// jQuery("#<?php echo $grid_id?>").jqGrid('filterToolbar',{stringResult: true,searchOnEnter : false}); 
		<?php } ?>

		<?php if ($this->actions["showhidecolumns"] !== false) { ?>
		// show/hide columns
		jQuery("#<?php echo $grid_id?>").jqGrid('navButtonAdd',"#<?php echo $grid_id."_pager"?>",{caption:"Columns",title:"Hide/Show Columns", buttonicon :'ui-icon-note',
			onClickButton:function(){
				jQuery("#<?php echo $grid_id?>").jqGrid('setColumns'); 
			} 
		});
		<?php } ?>
		
		<?php if ($this->actions["export"] === true) { $order_by = "&sidx=".$this->options["sortname"]."&sord=".$this->options["sortorder"]; ?>
		// Export to what is defined in file
		jQuery("#<?php echo $grid_id?>").jqGrid('navButtonAdd',"#<?php echo $grid_id."_pager"?>",{caption:"Export",title:"Export", buttonicon :'ui-icon-extlink',
			onClickButton:function(){
				if ("<?php echo $this->options["url"]?>".indexOf("?") != -1)
					location.href = "<?php echo $this->options["url"]?>" + "&export=1&jqgrid_page=1<?php echo $order_by?>";
				else
					location.href = "<?php echo $this->options["url"]?>" + "?export=1&jqgrid_page=1<?php echo $order_by?>";
			} 
		});
		<?php } ?>
			
		<?php if (isset($this->actions["export_excel"]) && $this->actions["export_excel"] === true) { ?>
		// Export to excel
		jQuery("#<?php echo $grid_id?>").jqGrid('navButtonAdd',"#<?php echo $grid_id."_pager"?>",{caption:"Excel",title:"Excel", buttonicon :'ui-icon-extlink',
			onClickButton:function(){
				if ("<?php echo $this->options["url"]?>".indexOf("?") != -1)
					location.href = "<?php echo $this->options["url"]?>" + "&export=1&jqgrid_page=1&export_type=excel<?php echo $order_by?>";
				else
					location.href = "<?php echo $this->options["url"]?>" + "?export=1&jqgrid_page=1&export_type=excel<?php echo $order_by?>";
			} 
		});
		<?php } ?>
			
		<?php if (isset($this->actions["export_pdf"]) && $this->actions["export_pdf"] === true) { ?>
		// Export to pdf
		jQuery("#<?php echo $grid_id?>").jqGrid('navButtonAdd',"#<?php echo $grid_id."_pager"?>",{caption:"PDF",title:"PDF", buttonicon :'ui-icon-extlink',
			onClickButton:function(){
				if ("<?php echo $this->options["url"]?>".indexOf("?") != -1)
					location.href = "<?php echo $this->options["url"]?>" + "&export=1&jqgrid_page=1&export_type=pdf<?php echo $order_by?>";
				else
					location.href = "<?php echo $this->options["url"]?>" + "?export=1&jqgrid_page=1&export_type=pdf<?php echo $order_by?>";
			} 
		});		
		<?php } ?>	

		<?php if (isset($this->actions["export_csv"]) && $this->actions["export_csv"] === true) { ?>
		// Export to csv
		jQuery("#<?php echo $grid_id?>").jqGrid('navButtonAdd',"#<?php echo $grid_id."_pager"?>",{caption:"CSV",title:"CSV", buttonicon :'ui-icon-extlink',
			onClickButton:function(){
				if ("<?php echo $this->options["url"]?>".indexOf("?") != -1)
					location.href = "<?php echo $this->options["url"]?>" + "&export=1&jqgrid_page=1&export_type=csv<?php echo $order_by?>";
				else
					location.href = "<?php echo $this->options["url"]?>" + "?export=1&jqgrid_page=1&export_type=csv<?php echo $order_by?>";
			} 
		});		
		<?php } ?>
				
		function link_date_picker(el,fmt,toolbar)
		{
			toolbar = toolbar || 0;
			setTimeout(function(){
				if(jQuery.ui) 
				{ 
					if(jQuery.ui.datepicker) 
					{ 
						if (toolbar == 0) jQuery(el).after('<button>Calendar</button>').next().button({icons:{primary: 'ui-icon-calendar'}, text:false}).css({'font-size':'69%'}).click(function(e){jQuery(el).datepicker('show');return false;});
						jQuery(el).datepicker({
												"disabled":false,
												"dateFormat":fmt,
												"onSelect": function (dateText, inst) 
															{
																if (toolbar)
																{
											                    	setTimeout(function () {
											                        jQuery("#<?php echo $grid_id?>")[0].triggerToolbar();
											                    	}, 50);
																}
										                	}
        									});
						jQuery('.ui-datepicker').css({'font-size':'69%'}); 
					} 
				}
			},300);
		}				

		function link_datetime_picker(el,fmt)
		{
			setTimeout(function(){
				if(jQuery.ui) 
				{ 
					if(jQuery.ui.datepicker) 
					{
						jQuery(el).after('<button>Calendar</button>').next().button({icons:{primary: 'ui-icon-calendar'}, text:false}).css({'font-size':'69%'}).click(function(e){jQuery(el).datetimepicker('show');return false;});
						jQuery(el).datetimepicker({"disabled":false,"dateFormat":fmt});
						jQuery('.ui-datepicker').css({'font-size':'69%'});
					}
				}
			},100);
		}	

		function link_editor(el)
		{
			setTimeout(function(){
				var editor = CKEDITOR.replace( el, {
					on: {
						change: function(){ jQuery(el).val(editor.getData()); }
					},
					height: '100px'
				});
			},100);
		}

		function link_autocomplete(el,update_field)
		{
			setTimeout(function()
			{
				if(jQuery.ui) 
				{ 
					if(jQuery.ui.autocomplete)
					{
						jQuery(el).autocomplete({	"appendTo":"body","disabled":false,"delay":300,
													"minLength":1,
													"source":function (request, response)
															{
																request.element = el.name;
																request.oper = 'autocomplete';
																$.ajax({
																	url: "<?php echo $this->options["url"]?>",
																	dataType: "json",
																	data: request,
																	type: "POST",
																	error: function(res, status) {
																		alert(res.status+" : "+res.statusText+". Status: "+status);
																	},
																	success: function( data ) {
																		response( data );
																	}
																});
															},
													"select":function (event, ui)
															{
																// change function to set target value
																var ival;
																if(ui.item) {
																	ival = ui.item.id || ui.item.value;
																}
																if(ival) {
																	jQuery("input[name='"+update_field+"']").val(ival);
																} else {
																	jQuery("input[name='"+update_field+"']").val("");
																	if("1" == "true"){
																	this.value = "";
																	}
																}
															}
												});

						jQuery(el).autocomplete('widget').css('font-size','11px');

					} // if jQuery.ui.autocomplete
				} // if jQuery.ui
			},200); // setTimeout
		} // link_autocomplete

		<?php if ($this->require_upload_ajax) { ?>
		function ajaxFileUpload(field,upload_url)
		{
			//starting setting some animation when the ajax starts and completes
			jQuery("#loading")
			.ajaxStart(function(){
				jQuery(this).show();
			})
			.ajaxComplete(function(){
				jQuery(this).hide();
			});
			
			/*
			prepareing ajax file upload
			url: the url of script file handling the uploaded files
						fileElementId: the file type of input element id and it will be the index of  $_FILES Array()
			dataType: it support json, xml
			secureuri:use secure protocol
			success: call back function when the ajax complete
			error: callback function when the ajax failed
			*/
			jQuery.ajaxFileUpload
			(
				{
					url:upload_url, 
					secureuri:false,
					fileElementId:field,
					dataType: 'json',
					success: function (data, status)
					{
						if(typeof(data.error) != 'undefined')
						{
							if(data.error != '')
							{
								alert(data.error);
							}else
							{
								alert(data.msg);
							}
						}
					},
					error: function (data, status, e)
					{
						alert(e);
					}
				}
			)
			return false;
		}  	
		<?php } ?>
		
		jQuery("#<?php echo $grid_id?>").jqGrid('gridResize',{});
		
		jQuery("#<?php echo $grid_id?>").jqGrid('setFrozenColumns');		
	<?php
	}

	function prepare_sql($sql,$db)
	{
		if (strpos($db,"mssql") !== false)
		{
			$sql = preg_replace("/SELECT (.*) LIMIT ([0-9]+) OFFSET ([0-9]+)/i","select top ($2+$3) $1",$sql);
			#pr($sql,1);
		}
		else if (strpos($db,"oci8") !== false || strpos($db,"db2") !== false)
		{
			$sql = preg_match("/(.*) LIMIT ([0-9]+) OFFSET ([0-9]+)/i",$sql,$matches);
		
			$query = $matches[1];
			$limit = $matches[2];
			$offest = $matches[3];

			$offset_min = $offest;
			$offset_max = $offest + $limit;
			
			$sql = "
				SELECT * FROM (
					SELECT rownum rnum,a.*
					FROM ($query) a
				)
				WHERE rnum BETWEEN $offset_min AND $offset_max
			";			
		}
		return $sql;
	}

	// replace any param in data pattern e.g. http://domain.com?id={id} given that, there is a $col["name"] = "id" exist
	function replace_row_data($row,$str)
	{
		foreach($this->options["colModel"] as $link_c)
		{
			$link_row_data = $row[$link_c["name"]];
			$str = str_replace("{".$link_c["name"]."}", $link_row_data, $str);
		}

		return $str;
	}
}

# In PHP 5.2 or higher we don't need to bring this in
if (!function_exists('json_encode')) 
{
	require_once 'JSON.php';
	function json_encode($arg)
	{
		global $services_json;
		if (!isset($services_json)) {
			$services_json = new Services_JSON();
		}
		return $services_json->encode($arg);
	}

	function json_decode($arg)
	{
		global $services_json;
		if (!isset($services_json)) {
			$services_json = new Services_JSON();
		}
		return $services_json->decode($arg);
	}
}

/**
 * Common function to display errors
 */
if (!function_exists('phpgrid_error'))
{
	function phpgrid_error($msg)	
	{
		header('HTTP/1.1 500 Internal Server Error');
		die($msg);	
	}
}

/**
 * Internal debug function
 */
if (!function_exists('phpgrid_pr'))
{
	function phpgrid_pr($arr, $exit=0)
	{
		echo "<pre>";
		print_r($arr);
		echo "</pre>";
		
		if ($exit)
			die;
	}
}

/**
 * Function to encode JS function reference from PHP array
 * http://www.php.net/manual/en/function.json-encode.php#105749
 */
function json_encode_jsfunc($input=array(), $funcs=array(), $level=0)
{
	foreach($input as $key=>$value)
	{
		if (is_array($value))
		{
			$ret = json_encode_jsfunc($value, $funcs, 1);
			$input[$key]=$ret[0];
			$funcs=$ret[1];
		}
		else
		{
			if (substr($value,0,8)=='function')
			{
				$func_key="#".uniqid()."#";
				$funcs[$func_key]=$value;
				$input[$key]=$func_key;
			}
			// for json data, incase of local array
			else if (substr($value,0,2)=='[{')
			{
				$func_key="#".uniqid()."#";
				$funcs[$func_key]=$value;
				$input[$key]=$func_key;
			}
		}
	}
  	if ($level==1)
	{
		return array($input, $funcs);
	}
  	else
	{
		$input_json = json_encode($input);
	  	foreach($funcs as $key=>$value)
		{
			$input_json = str_replace('"'.$key.'"', $value, $input_json);
		}
	  	return $input_json;
	}
}