<?php

require_once('class_dicom.php');

define('STORAGE', 'D:/wamp/www/dcm_STOR');

get_directory("D:/wamp/www/tmp_dir");
print("<pre>");


function insert_pa($name,$id,$sex,$age,$weight,$birth)
{
	$con = mysql_connect("localhost","root","");
	if (!$con)
	  {
	  die('Could not connect: ' . mysql_error());
	  }
	mysql_query("SET NAMES 'utf8'");
	mysql_select_db("imagedata", $con);
	$sql = "SELECT PatientID from patientinfo WHERE PatientName = '".$name."' AND TemporaryID = '".$id."' ";
	
	$result = mysql_query($sql) or die('MySQL query error select pa');
	
	print_r(mysql_num_rows($result));
	if(mysql_num_rows($result)>0)
	{
		$row = mysql_fetch_row($result);
	 	return $row[0];;
	}
	else
	{
		$sql = "INSERT INTO patientinfo(TemporaryID,PatientName,PatientSex,PatientAge,PatientWeight,PatientBirthDate) VALUES('".$id."','".$name."' ,'".$sex."' ,'".$age."' ,'".$weight."','".$birth."' )";
	//echo $sql;
		mysql_query($sql) or die('MySQL query error insert pa');
		return  mysql_insert_id();
	}
	mysql_close();
}

function insert_se($pa_id,$series_id,$appt_date,$modality,$device,$descp,$operator,$thick,$spacing)
{
	$con = mysql_connect("localhost","root","");
	if (!$con)
	  {
	  die('Could not connect: ' . mysql_error());
	  }
	mysql_query("SET NAMES 'utf8'");
	mysql_select_db("imagedata", $con);
	$sql = "SELECT ScanID from scansequence WHERE Temporary_ID = '".$series_id."' AND Scandate = '".$appt_date."' ";
	$result = mysql_query($sql) or die('MySQL query error select se');
	if(mysql_num_rows($result)>0)
	{
		$row = mysql_fetch_row($result);
	 	return $row[0];;
	}
	else
	{
		$sql = "INSERT INTO scansequence(Temporary_ID,Patient_ID,Scandate,ScanType,ScanDevice,SequenceDescription,ScanOperator,Thick,Spacing) VALUES('".$series_id."','".$pa_id."' ,'".$appt_date."' ,'".$modality."' ,'".$device."','".$descp."','".$operator."','".$thick."','".$spacing."' )";
	//echo $sql;
		mysql_query($sql) or die('MySQL query error insert se');
		return  mysql_insert_id();
	}
	mysql_close();
}



function process_file($file) {

  if(!is_dcm($file)) {
    print("Not a DICOM file: $file\n");
    unlink($file);
    return(0);
  }

  $d = new dicom_tag;
  $d->file = $file;
  $d->load_tags();

  $name = $d->get_tag('0010', '0010');
  $id = $d->get_tag('0010', '0020');
  $modality = $d->get_tag('0008', '0060');
  $appt_date = $d->get_tag('0008', '0020');
  $sop_id = $d->get_tag('0002', '0003');
  $sex = $d->get_tag('0010', '0040');
  $age = $d->get_tag('0010', '1010');
  $weight = $d->get_tag('0010', '1030');
  $birth = $d->get_tag('0010', '0030');
  $birth = substr($birth,0,4).'-'.substr($birth,4,2).'-'.substr($birth,6,2);
  $appdate = substr($appt_date,0,4).'-'.substr($appt_date,4,2).'-'.substr($appt_date,6,2);
  $series_id = $d->get_tag('0020', '0052');
  $device = $d->get_tag('0008', '0070');
  $descp = $d->get_tag('0008', '1080');
  $operator = $d->get_tag('0018', '1030');
  $model_id = $d->get_tag('0008', '1090');
  $diagnoses = $d->get_tag('0010', '0030');
  $thick = $d->get_tag('0018', '0050');
  $spacing = $d->get_tag('0028', '0030');
  $device = $device.$model_id;
  


  $pa_id = insert_pa($name,$id,$sex,$age,$weight,$birth);
  $se_id = insert_se($pa_id,$series_id,$appdate,$modality,$device,$descp,$operator,$thick,$spacing);
  $year = date('Y', strtotime($appt_date));
  $month = date('m', strtotime($appt_date));
  $day = date('d', strtotime($appt_date));

  $storage = STORAGE . "/".str_pad($pa_id, 5, "0", STR_PAD_LEFT)."_".$name;
  if(!file_exists($storage)) {
    mkdir($storage);
  }
  $storage = $storage . "/".str_pad($se_id, 8, "0", STR_PAD_LEFT)."_".$modality."_".$appdate;
  if(!file_exists($storage)) {
    mkdir($storage);
  }

/*  $name = str_replace('^', '_', $name);
  $arr_replace = array('^', "'", '"', '`', '/', '\\', '?', ':', ';');
  foreach($arr_replace as $replace) {
    $name = str_replace($replace, '', $name);
    $id = str_replace($replace, '', $id);
  }

  $storage = $storage . "/$name" . "_$id";
  if(!file_exists($storage)) {
    mkdir($storage);
  }*/

  $new_file = $modality . "_" . $sop_id . ".dcm";

  if(file_exists("$storage/$new_file")) {
    $new_file = $modality . "_" . $sop_id . "_" . rand(1, 1000) . ".dcm";
  }

//  print "$storage/$new_file\n";

  if(!rename($file, "$storage/$new_file")) {
    print "Failed $file -> $storage/$new_file";
    exit;
  }
  print ".";

//  print "$name - $storage\n";
  //exit;
}

function is_dir_empty($dir) {
  if (!is_readable($dir)) return NULL;
  return (count(scandir($dir)) == 2);
}

function get_directory($dir, $level = 0) {
  $ignore = array( 'cgi-bin', '.', '..' );
  $dh = @opendir($dir);
  while( false !== ( $file = readdir($dh))){
    if( !in_array( $file, $ignore ) ){
      if(is_dir("$dir/$file")) {
        echo "\n$file\n";
        get_directory("$dir/$file", ($level+1));
      }
      else {
        //echo "$spaces $file\n";
        process_file("$dir/$file");
      }
    }
  }

  closedir( $dh );

  if(is_dir_empty($dir) && $dir != "D:/wamp/www/tmp_dir") {
    //print "\n-= Removing $dir =-\n";
    rmdir($dir);

  }

}

?>