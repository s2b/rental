<?php

class MY_Session extends CI_Session
{
	var $user_table_name = 'users';
	var $logged_in = false;

	function MY_Session($params = array())
	{
		parent::CI_Session($params);

		$this->CI->load->database();

		if (isset($this->userdata['user_id']))
		{
			$this->logged_in = true;
		}
	}

	function user_login($email, $password)
	{
		$this->CI->db->from($this->user_table_name);
		$this->CI->db->where('user_status', USER_ACTIVE);
		$this->CI->db->where('user_email', $email);
		$this->CI->db->where('user_password', sha1($password));
		$this->CI->db->limit(1);

		$query = $this->CI->db->get();
		$result = $query->result();

		if (!isset($result[0]))
		{
			return false;
		}
		
		$this->set_userdata($result[0]);
		$this->logged_in = true;

		$this->CI->db->where('user_id', $this->userdata['user_id']);
		$this->CI->db->update($this->user_table_name, array('user_last_visit' => date('Y-m-d H:i:s')));

		return true;
	}

	function user_logout()
	{
		$this->sess_destroy();
		$this->logged_in = false;
	}
}

?>