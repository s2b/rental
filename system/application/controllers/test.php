<?php

class Test extends Controller
{
	function index()
	{
		$this->load->view('header');

		$this->load->library('calendar');

		$cal = new MY_Calendar();
		$cal->add_date(5, '2:00', '5:00', 'Test');
		$cal->add_date(8, '3:00', '6:00', 'Test 2');

		$cal->generate();

		$this->load->view('footer');
	}
}

?>