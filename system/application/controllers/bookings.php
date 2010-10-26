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
		// Verfügbare Aktionen
		$actions = array(
			BOOKING_CONFIRMED => 'bestätigen',
			BOOKING_BORROWED => 'ausleihen',
			BOOKING_CLOSED => 'abschließen',
			BOOKING_DENIED => 'ablehnen',
			'' => '',
			BOOKING_DELETE => 'löschen');

		// Daten fürs View
		$data = array(
			'is_inventory' => true,
			'form_url' => 'bookings/action/inventory',
			'bookings' => $this->bookings_model->listing(),
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
		// Verfügbare Aktionen
		$actions = array(
			BOOKING_CONFIRMED => 'bestätigen',
			BOOKING_DENIED => 'ablehnen',
			'' => '',
			BOOKING_DELETE => 'löschen');

		// Daten fürs View
		$data = array(
			'is_inventory' => false,
			'form_url' => 'bookings/action/studio',
			'bookings' => $this->bookings_model->listing(true),
			'actions' => $actions
		);

		$this->load->view('header');
		$this->load->view('bookings/listing', $data);
		$this->load->view('footer');
	}

	function calendar()
	{
		if (!$this->ajax) {
			exit;
		}

		$prefs = array(
			'show_next_prev'  => TRUE,
			'next_prev_url'   => 'bookings/calendar'
		);

		$this->load->library('calendar', $prefs);

		$content = $this->load->view('bookings/calendar', array(
			'calendar' => $this->calendar->generate($this->uri->segment(3), $this->uri->segment(4))
		), true);

		echo json_encode(array('status' => 1, 'content' => $content));
	}

	/**
	 * Buchungsstatus bearbeiten oder Buchung löschen
	 */
	function action()
	{
		$actions = $this->input->post('action');
		$update = $this->input->post('update');

		// Zurück, wenn Parameter fehlen oder abgebrochen wird
		if (!$actions || $this->input->post('cancel'))
		{
			redirect('bookings/' . $this->uri->segment(3));
		}

		// Buchung und neuen Status bestimmen
		$booking_id = (is_array($update)) ? key($update) : key($actions);
		$booking_status = $actions[$booking_id];

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

			if ($this->ajax)
			{
				// Wird per AJAX aufgerufen => JSON zurückgeben
				echo json_encode(array('status' => 1));
			}
			else
			{
				// Weiterleiten zur Auflistung
				redirect('bookings/' . $this->uri->segment(3));
			}
		}
		else
		{
			// Daten fürs View
			$data = array(
				'hidden_fields' => form_hidden('action[' . $booking_id . ']', BOOKING_DELETE),
				'is_inventory' => ($this->uri->segment(3) == 'inventory'),
				'form_url' => 'bookings/action/' . $this->uri->segment(3)
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
		if ($this->input->post('save'))
		{
			$booking_desc = $this->input->post('desc');

			// Buchungsstatus in der Datenbank aktualisieren
			$this->bookings_model->updateStatus($booking_id, $booking_status, $booking_desc);

			if ($this->ajax)
			{
				// Wird per AJAX aufgerufen => JSON zurückgeben
				echo json_encode(array('status' => 1));
			}
			else
			{
				// Weiterleiten zur Auflistung
				redirect('bookings/' . $this->uri->segment(3));
			}
		}
		else
		{
			// Daten fürs View
			$data = array(
				'new_status' => $this->bookings_model->status_text[$booking_status],
				
				'hidden_fields' => form_hidden('action[' . $booking_id . ']', $booking_status),
				'is_inventory' => ($this->uri->segment(3) == 'inventory'),
				'form_url' => 'bookings/action/' . $this->uri->segment(3)
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