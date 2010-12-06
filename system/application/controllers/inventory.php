<?php

class Inventory extends Controller
{
	function Inventory()
	{
		parent::Controller();

		// Nur für eingeloggte Benutzer
		if (!$this->session->logged_in)
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
		$this->load->view('inventory/listing');
		$this->load->view('footer');
	}
}

?>