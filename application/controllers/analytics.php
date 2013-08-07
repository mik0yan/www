<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Analytics extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *统计分析类 包括以下几点
	 * 		查看分类数据
	 * 		 
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/analytics.php */