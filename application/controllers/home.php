<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *用户类 包括以下几点
	 * 		验证用户登录信息
	 * 		分配用户查看权限
	 * 			记录用户操作历史	 * 
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->model('maccount','',TRUE);
	}
	
	function index()
	{
		$this->load->helper(array('form','url'));
		$session_data = $this->session->userdata('logged_in');
		$this->load->view('home_view', $session_data);
	}
	
	function pacs()
	{
		$this->load->helper(array('form','url'));
		$session_data = $this->session->userdata('logged_in');
		$this->load->view('pacs_view', $session_data);
	}	
	function analytics()
	{
		$this->load->helper(array('form','url'));
		$session_data = $this->session->userdata('logged_in');
		$this->load->view('pacs_view', $session_data);
	}	
	function report()
	{
		$this->load->helper(array('form','url'));
		$session_data = $this->session->userdata('logged_in');
		$this->load->view('pacs_view', $session_data);
	}	
}