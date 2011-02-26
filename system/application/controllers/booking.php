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
		if ($this->input->post('submit'))
		{
			if ($this->_add())
			{
				redirect('home#inventory');
			}
		}
		
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
		if ($this->input->post('submit'))
		{
			if ($this->_add(true))
			{
				redirect('home#studio');
			}
		}
		
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
	
	function _add($is_room = false)
	{
		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		
		$this->form_validation->set_rules('description', 'Verwendungszweck', 'required|min_length[3]|max_length[255]|xss_clean');
		$this->form_validation->set_rules('start', 'Start-Datum', 'required|int');
		$this->form_validation->set_rules('end', 'End-Datum', 'required|int');
		$this->form_validation->set_rules('inventory', 'Inventar', 'required');

		if (!$this->form_validation->run())
		{
			return false;
		}
		
		$info = array();
		$info['booking_desc'] = $this->input->post('description');
		$info['booking_start'] = date('Y-m-d H:i:s', mktime(
			(int) $this->input->post('start_hour'),
			(int) $this->input->post('start_min'),
			0,
			date('n', (int) $this->input->post('start')),
			date('j', (int) $this->input->post('start')),
			date('Y', (int) $this->input->post('start'))
		));
		$info['booking_end'] = date('Y-m-d H:i:s', mktime(
			(int) $this->input->post('end_hour'),
			(int) $this->input->post('end_min'),
			0,
			date('n', (int) $this->input->post('end')),
			date('j', (int) $this->input->post('end')),
			date('Y', (int) $this->input->post('end'))
		));
		
		$inventory = $this->input->post('inventory');
		
		$this->bookings_model->add($info, $inventory, $is_room);
		
		return true;
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