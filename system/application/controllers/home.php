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
		// bereits eingeloggt?
		if ($this->session->logged_in)
		{
			redirect('home');
		}
		
		if (!$this->ajax)
		{
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
			// zur Übersicht weiterleiten
			redirect('/home/');
		}

		$this->load->view('home/login');
		$this->load->view('footer');
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
		// bereits eingeloggt?
		if ($this->session->logged_in)
		{
			redirect('home');
		}
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('email', 'E-Mailadresse', 'required|valid_email|callback__forgot_email_check');
		
		if ($this->form_validation->run())
		{
			$email = $this->input->post('email');
			$token = $this->user_model->forgot_password($email);
			
			$link = base_url() . 'home/resetpassword?email=' . $email . '&token=' . $token;
			
			$this->load->library('notifications');
			$this->notifications->userForgotPassword($email, $link);
			
			$this->load->view('header');
			$this->load->view('home/lostpassword_success');				
			$this->load->view('footer');

			return;
		}
		
		$this->load->view('header');
		$this->load->view('home/lostpassword_form');
		$this->load->view('footer');
	}
	
	/**
	 * Passwort zurücksetzen
	 */
	function resetpassword()
	{
		// bereits eingeloggt?
		if ($this->session->logged_in)
		{
			redirect('home');
		}
		
		$email = (string) $this->input->get_post('email', true);
		$token = (string) $this->input->get_post('token');
		
		if (!$this->user_model->check_token($email, $token))
		{
			return;
		}
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('password', 'Passwort', 'required|min_length[4]|matches[password_confirm]');
		$this->form_validation->set_rules('password_confirm', 'Passwort (Bestätigung)', 'required');
		
		if ($this->form_validation->run())
		{
			$password = $this->input->post('password');
			
			$token = $this->user_model->reset_password($email, $password);
			
			$this->load->view('header');
			$this->load->view('home/resetpassword_success');
			$this->load->view('footer');
			
			return;
		}

		$data = array(
			'email' => $email,
			'hidden' => array('email' => $email, 'token' => $token)
		);
		
		$this->load->view('header');
		$this->load->view('home/resetpassword_form', $data);
		$this->load->view('footer');
	}
	
	/**
	 * Registrierung
	 */
	function register()
	{
		// bereits eingeloggt?
		if ($this->session->logged_in)
		{
			redirect('/home/');
		}

		$this->load->view('header');

		// Formularvalidierung vorbereiten
		$this->load->library('form_validation');

		$this->form_validation->set_rules('name', 'Name', 'required|min_length[3]|max_length[50]|xss_clean');
		$this->form_validation->set_rules('email', 'E-Mailadresse', 'required|valid_email|callback__register_email_check');
		$this->form_validation->set_rules('student_id', 'Matrikelnummer', 'required|exact_length[6]|integer');
		$this->form_validation->set_rules('semester', 'Semester', 'required|callback__semester_check');
		$this->form_validation->set_rules('password', 'Passwort', 'required|min_length[4]|matches[password_confirm]');
		$this->form_validation->set_rules('password_confirm', 'Passwort (Bestätigung)', 'required');

		// Formular validieren
		if ($this->form_validation->run())
		{
			$info = array(
				'user_name' => $this->input->post('name'),
				'user_email' => $this->input->post('email'),
				'user_password' => $this->input->post('password'),
				'user_student_id' => $this->input->post('student_id'),
				'semester_id' => $this->input->post('semester'));
			
			// Benutzer hinzufügen
			$this->load->model('user_model');
			$this->user_model->add($info);
			
			$this->load->library('notifications');
			$this->notifications->userRegistration($info['user_email']);
		
			// Nachricht anzeigen
			$this->load->view('home/register_success');
			$this->load->view('footer');

			return;
		}

		$data = array(
			'semesters' => $this->user_model->semester()
		);
		
		$this->load->view('home/register_form', $data);
		$this->load->view('footer');
	}
	
	function _semester_check($value)
	{
		if ($this->user_model->semester($value) !== '')
		{
			return true;
		}
		else
		{
			$this->form_validation->set_message('_semester_check', 'Das angegebene Semester ist ungültig.');
			return false;
		}
	}
	
	function _register_email_check($value)
	{
		if (!$this->user_model->check_email($value))
		{
			return true;
		}
		else
		{
			$this->form_validation->set_message('_register_email_check', 'Die eingegebene E-Mailadresse ist bereits registriert.');
			return false;
		}
	}
	
	function _forgot_email_check($value)
	{
		if (!$this->user_model->check_email($value))
		{
			$this->form_validation->set_message('_forgot_email_check', 'Die eingegebene E-Mailadresse ist nicht registriert.');
			return false;
		}
		else
		{
			return true;
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