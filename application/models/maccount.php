<?php
Class Maccount extends CI_Model
{
		/**
	 * Index Page for this controller.
	 *用户数据模型包括以下几个功能
	 *		登录查询
	 * 		注册查询
	 * 
	 */
	 
 function login($username, $password)
 {
   $this -> db -> select('UserID, UserName, UserPIN, UserType');
   $this -> db -> from('userauthority');
   $this -> db -> where('UserName', $username);
   $this -> db -> where('UserPIN', md5($password));
   $this -> db -> limit(1);

   $query = $this -> db -> get();

   if($query -> num_rows() == 1)
   {
     return $query->result();
   }
   else
   {
     return false;
   }
 }
 
 function checkuser($username)
 {
	$this -> db -> select('UserID, UserName');
	$this -> db -> from('userauthority');
	$this -> db -> where('UserName', $username);
	$this -> db -> limit(1);
   $query = $this -> db -> get();

   if($query -> num_rows() == 1)
   {
     return $query->result();
   }
   else
   {
     return false;
   }
 function checkemail($email)
 {
	$this -> db -> select('UserID, UserEmail');
	$this -> db -> from('userauthority');
	$this -> db -> where('UserEmail', $email);
	$this -> db -> limit(1);
   $query = $this -> db -> get();

   if($query -> num_rows() == 1)
   {
     return $query->result();
   }
   else
   {
     return false;
   }

 }
}
 function checkemail($email)
 {
	$this -> db -> select('UserID, UserEmail');
	$this -> db -> from('userauthority');
	$this -> db -> where('UserEmail', $email);
	$this -> db -> limit(1);
    $query = $this -> db -> get();

   if($query -> num_rows() == 1)
   {
     return $query->result();
   }
   else
   {
     return false;
   }
 function checkemail($email)
 {
	$this -> db -> select('UserID, UserEmail');
	$this -> db -> from('userauthority');
	$this -> db -> where('UserEmail', $email);
	$this -> db -> limit(1);
   $query = $this -> db -> get();

   if($query -> num_rows() == 1)
   {
     return $query->result();
   }
   else
   {
     return false;
   }

 }
}
 function add_account($username,$email,$password)
 {
 	$this->load->database();	
 	$data = array(
		'UserName' =>$username,
		'UserType' =>'guest',
		'UserRegDate'=>date("Y-m-d h:i:s"),
		'UserlastLogDate'=>date("Y-m-d h:i:s"),
		'UserlastLogIP'=>getenv('REMOTE_ADDR'),
		'UserEmail' =>$email,
		'UserPIN'=>md5($password)
	);
	$this->db->insert('userauthority',$data);
 	return TRUE;
 }

}
?>