<?php

class Bookings extends Controller
{
	function Bookings()
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

		$this->load->helper('form_helper');
		$this->load->model('bookings_model');

		$this->ajax = (bool) $this->input->post('ajax');
	}

	function index()
	{
		$this->inventory();
	}

	/**
	 * Gebuchtes Inventar auflisten
	 */
	function inventory()
	{
		if ($this->uri->segment(3) == 'action')
		{
			$this->_action();
			return;
		}

		// Verfügbare Aktionen
		$actions = array(
			BOOKING_CONFIRMED => 'bestätigen',
			BOOKING_BORROWED => 'ausleihen',
			BOOKING_CLOSED => 'abschließen',
			BOOKING_DENIED => 'ablehnen',
			'' => '------',
			BOOKING_DELETE => 'löschen');

		$bookings = $this->bookings_model->listing(false, true);

		if ($this->ajax)
		{
			echo json_encode(array('status' => 1, 'content' => $this->_calendar('inventory', $bookings)));
			return;
		}

		// Daten fürs View
		$data = array(
			'calendar' => $this->_calendar('inventory', $bookings),
			'is_inventory' => true,
			'form_url' => 'bookings/inventory/action',
			'bookings' => $this->_bookings_categories($bookings),
			'actions' => $actions
		);

		$this->load->view('header');
		$this->load->view('bookings/listing', $data);
		$this->load->view('footer');
	}

	/**
	 * Studio-Reservierungen auflisten
	 */
	function studio()
	{
		if ($this->uri->segment(3) == 'action')
		{
			$this->_action();
			return;
		}

		// Verfügbare Aktionen
		$actions = array(
			BOOKING_CONFIRMED => 'bestätigen',
			BOOKING_DENIED => 'ablehnen',
			'' => '',
			BOOKING_DELETE => 'löschen');

		$bookings = $this->bookings_model->listing(true, true);

		if ($this->ajax)
		{
			echo json_encode(array('status' => 1, 'content' => $this->_calendar('studio', $bookings)));
			return;
		}

		// Daten fürs View
		$data = array(
			'calendar' => $this->_calendar('studio', $bookings),
			'is_inventory' => false,
			'form_url' => 'bookings/studio/action',
			'bookings' => $this->_bookings_categories($bookings),
			'actions' => $actions
		);

		$this->load->view('header');
		$this->load->view('bookings/listing', $data);
		$this->load->view('footer');
	}

	function _calendar($page, $bookings)
	{
		$year = (int) $this->uri->segment(3, date('Y'));
		$month = (int) $this->uri->segment(4, date('m'));

		$prefs = array(
			'first_weekday' => 'Mon',
			'show_next_prev' => true,
			'next_prev_url' => base_url() . "bookings/$page"
		);

		$this->load->library('calendar2', $prefs);

		$this->calendar2->select_month($month, $year);

		foreach ($bookings as $booking)
		{
			if ($booking->status == BOOKING_CONFIRMED || $booking->status == BOOKING_BORROWED)
			{
				$this->calendar2->add_date_timestamp(strtotime($booking->start), strtotime($booking->end), $booking->desc, array('id' => $booking->id, 'status' => $booking->status));
			}
		}
		
		return $this->calendar2->generate();
	}

	function _bookings_categories($bookings)
	{
		$categories = array();
		foreach ($bookings as $booking)
		{
			if (!isset($categories[$booking->status]))
			{
				$status = (in_array($booking->status, array(BOOKING_CLOSED, BOOKING_DENIED))) ? BOOKING_ARCHIVE : $booking->status;
				
				$categories[$status] = new stdClass();
				$categories[$status]->bookings = array();

				switch ($status)
				{
					case BOOKING_NEW:
						$categories[$status]->title = 'offene Buchungen';
					break;

					case BOOKING_CONFIRMED:
						$categories[$status]->title = 'bestätigte Buchungen';
					break;

					case BOOKING_BORROWED:
						$categories[$status]->title = 'ausgeliehene Buchungen';
					break;

					case BOOKING_ARCHIVE:
						$categories[$status]->title = 'Buchungsarchiv';
					break;
				}
			}

			$categories[$status]->bookings[] = $booking;
		}

		return $categories;
	}

	/**
	 * Buchungsstatus bearbeiten oder Buchung löschen
	 */
	function _action()
	{		
		$page = $this->uri->segment(2);
		
		$actions = $this->input->post('action');
		$update = $this->input->post('update');

		// Zurück, wenn Parameter fehlen oder abgebrochen wird
		if (!$actions || $this->input->post('cancel'))
		{
			redirect("bookings/$page");
		}

		// Buchung und neuen Status bestimmen
		$booking_id = (is_array($update)) ? key($update) : key($actions);
		$booking_status = $actions[$booking_id];

		if (!$this->bookings_model->check($booking_id))
		{
			return;
		}

		// Status oder löschen?
		if ($booking_status == BOOKING_DELETE)
		{
			$this->_delete($booking_id);
		}
		else
		{
			$this->_status($booking_id, $booking_status);
		}
	}

	/**
	 * Buchung löschen
	 * @param int $booking_id ID der betroffenen Buchung
	 */
	function _delete($booking_id)
	{
		if ($this->input->post('delete'))
		{
			// Buchung aus der Datenbank löschen
			$this->bookings_model->delete($booking_id);

			// Weiterleiten zur Auflistung
			redirect('bookings/' . $this->uri->segment(2));
		}
		else
		{
			// Daten fürs View
			$data = array(
				'hidden_fields' => form_hidden('action[' . $booking_id . ']', BOOKING_DELETE),
				'is_inventory' => ($this->uri->segment(2) == 'inventory'),
				'form_url' => 'bookings/' . $this->uri->segment(2) . '/action/'
			);

			if ($this->ajax)
			{
				// View wird per AJAX aufgerufen => JSON zurückgeben
				echo json_encode(array('status' => 1, 'content' => $this->load->view('bookings/delete', $data, true)));
			}
			else
			{
				$this->load->view('header');
				$this->load->view('bookings/delete', $data);
				$this->load->view('footer');
			}
		}
	}

	/**
	 * Buchungsstatus bearbeiten
	 * @param <type> $booking_id ID der betroffenen Buchung
	 * @param <type> $booking_status neuer Status der Buchung
	 */
	function _status($booking_id, $booking_status)
	{
		$is_inventory = ($this->uri->segment(2) == 'inventory');
		
		if ($this->input->post('save'))
		{
			$booking_desc = $this->input->post('desc');

			// Buchungsstatus in der Datenbank aktualisieren
			$this->bookings_model->updateStatus($booking_id, $booking_status, $booking_desc);
			
			$email = $this->bookings_model->getNotificationEmail($booking_id);
			
			$this->load->library('notifications');
			if ($is_inventory)
			{
				if ($booking_status == BOOKING_CONFIRMED)
				{
					$this->notifications->inventoryBookingAccepted($email);
				}
				else if ($booking_status == BOOKING_DENIED)
				{
					$this->notifications->inventoryBookingDenied($email);
				}
			}
			else
			{
				if ($booking_status == BOOKING_CONFIRMED)
				{
					$this->notifications->studioBookingAccepted($email);
				}
				else if ($booking_status == BOOKING_DENIED)
				{
					$this->notifications->studioBookingDenied($email);
				}
			}

			// Weiterleiten zur Auflistung
			redirect('bookings/' . $this->uri->segment(2));
		}
		else
		{
			// Daten fürs View
			$data = array(
				'new_status' => $this->bookings_model->status_text[$booking_status],
				
				'hidden_fields' => form_hidden('action[' . $booking_id . ']', $booking_status),
				'is_inventory' => $is_inventory,
				'form_url' => 'bookings/' . $this->uri->segment(2) . '/action'
			);
			
			if ($this->ajax)
			{
				// View wird per AJAX aufgerufen => JSON zurückgeben
				echo json_encode(array('status' => 1, 'content' => $this->load->view('bookings/status', $data, true)));
			}
			else
			{
				$this->load->view('header');
				$this->load->view('bookings/status', $data);
				$this->load->view('footer');
			}
		}
	}
}

?>