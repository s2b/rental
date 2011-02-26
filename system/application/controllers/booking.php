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
		
		$this->load->helper('form_helper');
		$this->load->model('bookings_model');
		$this->load->model('inventory_model');
		$this->load->model('packages_model');
		
		$this->ajax = (bool) $this->input->post('ajax');
	}

	function index()
	{
		$this->inventory();
	}

	function inventory()
	{
		$bookings = $this->bookings_model->listing(false);
		
		$this->inventory_model->order = 'inventory_title asc';
		$inventory = $this->inventory_model->listing(INVENTORY_ACTIVE);
		
		$this->packages_model->order = 'package_title asc';
		$packages = $this->packages_model->listing(PACKAGE_ACTIVE, $inventory);
		
		$data = array(
			'calendar' => $this->_calendar('inventory', $bookings),
			'form_url' => 'booking/inventory',
			'is_inventory' => true,
			'inventory' => $inventory,
			'packages' => $packages
		);
		
		if ($this->ajax)
		{
			echo json_encode(array('status' => 1, 'content' => $this->_calendar('inventory', $bookings)));
			return;
		}
		
		$this->load->view('header');
		$this->load->view('booking/booking.php', $data);
		$this->load->view('footer');
	}

	function studio()
	{
		$bookings = $this->bookings_model->listing(true);
		
		$this->inventory_model->order = 'inventory_title asc';
		$inventory = $this->inventory_model->listing(INVENTORY_ACTIVE, true);
		
		$data = array(
			'calendar' => $this->_calendar('studio', $bookings),
			'form_url' => 'booking/studio',
			'inventory' => $inventory,
			'is_inventory' => false
		);
		
		if ($this->ajax)
		{
			echo json_encode(array('status' => 1, 'content' => $this->_calendar('studio', $bookings)));
			return;
		}
		
		$this->load->view('header');
		$this->load->view('booking/booking.php', $data);
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
				$this->calendar2->add_date_timestamp(strtotime($booking->start), strtotime($booking->end), 'Buchung', array('id' => $booking->id, 'status' => $booking->status));
			}
		}
		
		return $this->calendar2->generate();
	}
}

?>