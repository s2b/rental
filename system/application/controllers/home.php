<?php

class Home extends Controller
{
	function Home()
	{
		parent::Controller();

		$this->ajax = $this->input->post('ajax');
	}

	/**
	 * Übersichtsseite
	 */
	function index()
	{
		// Nur für eingeloggte Benutzer
		if (!$this->session->logged_in)
		{
			redirect('/home/login/');
		}
		
		$this->load->model('bookings_model');
		
		$data = array(
			'inventory_bookings' => $this->bookings_model->listing(false, true, true),
			'studio_bookings' => $this->bookings_model->listing(true, true, true));

		$this->load->view('header');
		$this->load->view('home/index', $data);
		$this->load->view('footer');
	}

	/**
	 * Login-Formular
	 */
	function login()
	{
		if (!$this->ajax)
		{
			// bereits eingeloggt?
			if ($this->session->logged_in)
			{
				redirect('/home/');
			}

			$this->load->view('header');
		}

		// Formularvalidierung vorbereiten
		$this->load->library('form_validation');

		$this->form_validation->set_rules('email', 'E-Mail', 'required');
		$this->form_validation->set_rules('password', 'Passwort', 'required');
		$this->form_validation->set_rules('-', '', 'callback__login_check');

		// Formular validieren
		if ($this->form_validation->run())
		{
			if (!$this->ajax)
			{
				// zur Übersicht weiterleiten
				redirect('/home/');
			}
			else
			{
				// Erfolg per JSON zurückgeben
				echo json_encode(array('status' => 1));
			}
		}

		if (!$this->ajax)
		{
			$this->load->view('home/login');
			$this->load->view('footer');
		}
		else
		{
			// Aufruf per AJAX => Validierungsfehler im JSON-Objekt zurückgeben
			echo json_encode(array('status' => 0, 'content' => validation_errors()));
		}
	}

	/**
	 * Logout
	 */
	function logout()
	{
		if ($this->session->logged_in)
		{
			$this->load->view('header');

			$this->session->user_logout();

			$this->load->view('footer');
		}

		redirect('/home/login/');
	}

	/**
	 * Passwort vergessen
	 */
	function lostpassword()
	{
		$this->load->view('header');

		$this->load->view('footer');
	}
	
	/**
	 * Registrierung
	 */
	function register()
	{
		if (!$this->ajax)
		{
			// bereits eingeloggt?
			if ($this->session->logged_in)
			{
				redirect('/home/');
			}

			$this->load->view('header');
		}

		// Formularvalidierung vorbereiten
		$this->load->library('form_validation');

		$this->form_validation->set_rules('name', 'Name', 'required|min_length[3]|max_length[50]|xss_clean');
		$this->form_validation->set_rules('email', 'E-Mailadresse', 'required|valid_email');
		$this->form_validation->set_rules('password', 'Passwort', 'required|matches[password_confirm]');
		$this->form_validation->set_rules('password_confirm', 'Passwort (Bestätigung)', 'required');
		$this->form_validation->set_rules('student_id', 'Matrikelnummer', 'required|exact_length[6]|integer');
		$this->form_validation->set_rules('semester', 'Semester', 'required|callback__semester_check');

		/**
		 * @todo
		 */

		// Formular validieren
		if ($this->form_validation->run())
		{
			if (!$this->ajax)
			{
				// zur Übersicht weiterleiten
				redirect('/home/');
			}
			else
			{
				// Benutzer hinzufügen
				$this->user_model->add();
				
				// Erfolg per JSON zurückgeben
				echo json_encode(array('status' => 1));
			}
		}

		if (!$this->ajax)
		{
			$this->load->view('home/register');
			$this->load->view('footer');
		}
		else
		{
			// Aufruf per AJAX => Validierungsfehler im JSON-Objekt zurückgeben
			echo json_encode(array('status' => 0, 'content' => validation_errors()));
		}
	}

	/**
	 * Callback-Funktion für die Login-Validierung
	 * @return bool
	 */
	function _login_check()
	{
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		
		if ($this->session->user_login($email, $password))
		{
			return true;
		}
		else
		{
			$this->form_validation->set_message('_login_check', 'Die eingegebenen Zugangsdaten sind ungültig.');
			
			return false;
		}
	}
}

?>