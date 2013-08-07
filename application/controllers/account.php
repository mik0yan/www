<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends CI_Controller {

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

	public function index()
	{
		$this->load->helper(array('form','url'));
		$this->load->view('account/signin');
	}

	public function signup()
	{
		$this->load->helper(array('form','url'));
		$this->load->view('account/signup');
	}
	
	public function signout()
	{
		$this->session->unset_userdata('logged_in');
		session_destroy();
		redirect('home', 'refresh');
	}


	public function verifyaccount()
	{
		$this->load->library('form_validation');
		$this->load->helper(array('form','url'));
		$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');
		if($this->form_validation->run() == FALSE)
		{
			$this->load->view('account/signin');
		}
		else
		{
			redirect('home', 'refresh');
		}
	}

	public function rgtUser()
	{
		$this->load->library('form_validation');
		$this->load->helper(array('form','url'));
		$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean|callback_check_user');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|callback_check_email');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_add_account');
		if($this->form_validation->run() == FALSE)
		{
			$this->load->view('account/signup');
		}
		else
		{
			redirect('home', 'refresh');
		}
	}
	
	function add_account($password)
	{
		$username = $this->input->post('username');
		$email = $this->input->post('email');
		$addin = $this->maccount->add_account($username,$email,$password);
		$result = $this->maccount->login($username, $password);
		if($result)
		{
		 $sess_array = array();
		 foreach($result as $row)
		 {
		   $sess_array = array(
		     'id' => $row->UserID,
		     'username' => $row->UserName,
			 'usertype' => $row->UserType
		   );
		   $this->session->set_userdata('logged_in', $sess_array);
		 }
		 return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	function check_user($username)
	{
		$result = $this->maccount->checkuser($username);
		if($result)
		{
			$this->form_validation->set_message('check_user','用户名已存在');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
		
	}

	function check_email($email)
	{
		$result = $this->maccount->checkemail($email);
		if($result)
		{
			$this->form_validation->set_message('check_email','邮箱已被使用');
			return FALSE;
		}
		else
		{
			return TRUE;
		}
		
	}


	function check_database($password)
	{
		$username = $this->input->post('username');
		$result = $this->maccount->login($username, $password);
		if($result)
		{
		 $sess_array = array();
		 foreach($result as $row)
		 {
		   $sess_array = array(
		     'id' => $row->UserID,
		     'username' => $row->UserName,
			 'usertype' => $row->UserType
		   );
		   $this->session->set_userdata('logged_in', $sess_array);
		 }
		 return TRUE;
		}
		else
		{
		 $this->form_validation->set_message('check_database', '登录错误');
		 return false;
		}
	}	
}

/* End of file welcome.php */
/* Location: ./application/controllers/account.php */