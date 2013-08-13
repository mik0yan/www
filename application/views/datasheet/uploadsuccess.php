<html>
<head>
<title>Upload Form</title>
</head>
<body>

<h3>导入成功</h3>

<ul>
<?php 
//require_once(BASEPATH.'../class_dicom/class_dicom.php');
$file = $upload_data['orig_name'];
//echo $file."</br>";
chdir($upload_data['file_path']);
//echo getcwd()."</br>";

if(is_dcm($file))
{
	$d = new dicom_tag;
	$d->file = $file;
	$d->load_tags();
	$info['name'] = $d->get_tag('0010', '0010');
	$info['id'] = $d->get_tag('0010', '0020');
	$info['birth'] = $d->get_tag('0010', '0030');
	$info['sex'] = $d->get_tag('0010', '0040');
	$info['age'] = $d->get_tag('0010', '1010');
	$info['height'] = $d->get_tag('0010', '1020');
	$info['weight'] = $d->get_tag('0010', '1030');
	$info['ethnic'] = $d->get_tag('0010', '2160');
	$info['diagnoses'] = $d->get_tag('0008', '1080');
	$info['modality'] = $d->get_tag('0008', '0060');
	$info['appt_date'] = $d->get_tag('0008', '0020');
	$info['sop_id'] = $d->get_tag('0002', '0003');
	$info['number'] = $d->get_tag('0020', '0013');
	$info['row'] = $d->get_tag('0028', '0010');
	$info['coloumn'] = $d->get_tag('0028', '0011');
	$info['anatomy'] = $d->get_tag('0018', '0015');
	$info['position'] = $d->get_tag('0008', '1030');
	$info['manufacturer'] = $d->get_tag('0008', '0070');
	$info['model_id'] = $d->get_tag('0008', '1090');
	print("<pre>");
	print_r($info);
	print_r($d->tags);
	echo $info['name'].'('.$info['birth'].'):'.$info['number'].'幅影像资料已导入';
}
else
{
	echo "not dicom file";
}
 ?>
</ul>

<p><?php echo anchor('datasheet/newinstance', '上传新的文件'); ?></p>

</body>
</html>