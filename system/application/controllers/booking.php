<?php

class Booking extends Controller
{
	function Booking()
	{
		parent::Controller();

		// Nur für eingeloggte Benutzer
		if (!$this->session->logged_in)
		{
			redirect('/home/login');
		}
	}

	function index()
	{
		$this->inventory();
	}

	function inventory()
	{
		$this->load->view('header');

		$this->load->view('footer');
	}

	function studio()
	{
		$this->load->view('header');

		$this->load->view('footer');
	}
}

?>