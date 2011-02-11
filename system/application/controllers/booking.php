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
		
		$this->load->model('bookings_model');
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
	
	function _calendar($page, $bookings)
	{
		$year = (int) $this->uri->segment(3, date('Y'));
		$month = (int) $this->uri->segment(4, date('m'));

		$prefs = array(
			'first_weekday' => 'Mon',
			'show_next_prev' => true,
			'next_prev_url' => base_url() . "booking/$page"
		);

		$this->load->library('calendar2', $prefs);

		$this->calendar2->select_month($month, $year);

		foreach ($bookings as $booking)
		{
			if ($booking->status == BOOKING_CONFIRMED || $booking->status == BOOKING_BORROWED)
			{
				$this->calendar2->add_date_timestamp(strtotime($booking->start), strtotime($booking->end), 'belegt', array('id' => $booking->id, 'status' => $booking->status));
			}
		}
		
		return $this->calendar2->generate();
	}
}

?>