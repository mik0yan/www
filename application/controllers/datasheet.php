<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Datasheet extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *导入数据:
	 *		增、删、改患者数据
	 * 		增、删、改扫描记录、上传扫描图片
	 * 		填写测量结果数据
	 */
	public function index()
	{
		$this->load->view('datasheet/subjects');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/datasheet.php */