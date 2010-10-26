<?php

class Users extends Controller
{
	function Users()
	{
		parent::Controller();

		// Nur für eingeloggte Benutzer
		if (!$this->session->userdata('user_id'))
		{
			redirect('/home/login');
		}
		// Nur für Administratoren
		else if ($this->session->userdata('user_role') != USER_ROLE_ADMIN)
		{
			redirect('/home');
		}
	}

	function index()
	{
		$this->load->view('header');

		$this->load->view('footer');
	}
}

?>