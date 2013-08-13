<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Datasheet extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *导入数据:
	 *		增、删、改患者数据
	 * 		增、删、改扫描记录、上传扫描图片
	 * 		填写测量结果数据
	 */
	 function __construct()
	 {
	 	require_once(BASEPATH.'../class_dicom/class_dicom.php');
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->model('mdicom','',TRUE);
		//$this->load->library(array('class_dicom'));
	 }
	 
 	public function subject()
	{
		$this->load->view('datasheet/subjects');
	}

	public function subjectbyg()
	{
		$this->load->view('datasheet/subjectsbyg');
	}
	public function instance()
	{
		$this->load->view('datasheet/instance');
	}
	public function instancebyg()
	{
		$this->load->view('datasheet/instancebyg');
	}
	public function newinstance()
	{
		$this->load->library('upload');
		$this->load->view('datasheet/newinstance', array('error' => ' ' ));
	}
	
	public function do_upload()
	{
		$config['upload_path'] = './tmp_dir/';
		$config['allowed_types'] = '*';
		
		$this->load->library('upload', $config);
		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());
			
			$this->load->view('datasheet/newinstance', $error);
		} 
		else
		{
			$data = array('upload_data' => $this->upload->data());
			//require_once(BASEPATH.'../class_dicom/class_dicom.php');
			$filedata = $this->upload->data();
			chdir($filedata['file_path']);
			if($isdcm = is_dcm($filedata['orig_name']))
			{
				$d = new dicom_tag;
				$d->file = $filedata['orig_name'];
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
				$info['sop_id'] = $d->get_tag('0020', '0052');
				$info['number'] = $d->get_tag('0020', '0013');
				$info['row'] = $d->get_tag('0028', '0010');
				$info['coloumn'] = $d->get_tag('0028', '0011');
				$info['anatomy'] = $d->get_tag('0018', '0015');
				$info['position'] = $d->get_tag('0008', '1030');
				$info['manufacturer'] = $d->get_tag('0008', '0070');
				$info['operator'] = $d->get_tag('0018', '1030');
				$info['institution'] = $d->get_tag('0008', '0080');
				$info['address'] = $d->get_tag('0008', '0081');
				$info['model_id'] = $d->get_tag('0008', '1090');
			}
			chdir(BASEPATH.'../');
			if($isdcm)
			{
				if(!($info['insertid']=$this->mdicom->isexist_patient($info)))
				{
					$info['insertid'] = $this->mdicom->addpatient($info);	
				}
				if(!$this->mdicom->isexist_scan($info))
				{
					$this->mdicom->addscan($info);
					$this->load->view('datasheet/uploadsuccess', $data);
				}
				else
				{
					$error = array('error' => "序列已存在");
					$this->load->view('datasheet/newinstance', $error);
				}							
				

			}
			else
			{
				$error = array('error' => "请上传符合的dicom文件");
				$this->load->view('datasheet/newinstance', $error);
			}
		}
	}
	
	public function do_restore()
	{
		$storage =  "./tmp_dir/";
		get_directory("./dcm_STOR/");
	}
	
	function process_file($file) 
	{
	
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
		
		$year = date('Y', strtotime($appt_date));
		$month = date('m', strtotime($appt_date));
		$day = date('d', strtotime($appt_date));

		$storage = STORAGE . "/$year";
		if(!file_exists($storage)) {
			mkdir($storage);
		}
		$storage = $storage . "/$month";
		if(!file_exists($storage)) {
			mkdir($storage);
		}
		$storage = $storage . "/$day";
		if(!file_exists($storage)) {
			mkdir($storage);
		}
		$name = str_replace('^', '_', $name);
		$arr_replace = array('^', "'", '"', '`', '/', '\\', '?', ':', ';');
		foreach($arr_replace as $replace) {
			$name = str_replace($replace, '', $name);
			$id = str_replace($replace, '', $id);
		}

		$storage = $storage . "/$name" . "_$id";
		if(!file_exists($storage)) {
			mkdir($storage);
		}
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
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/datasheet.php */