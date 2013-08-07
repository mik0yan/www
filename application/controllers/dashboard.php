<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *仪表盘包括以下几个功能
	 *		添加、更改用户、删除用户操作
	 * 		数据后台管理 增、删、改 患者信息、扫描信息、测量结果
	 * 		测量内容管理 增、删、改 部位测量条目，测量分类、测量条目
	 * 
	 */
	public function index()
	{
		//if(1)
		$this->load->view('signin');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/dashboard.php */