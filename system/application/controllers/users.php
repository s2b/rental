<?php

class Users extends Controller
{
	function Users()
	{
		parent::Controller();

		// Nur für eingeloggte Benutzer
		if (!$this->session->userdata('user_id'))
		{
			redirect('/home/login');
		}
		// Nur für Administratoren
		else if ($this->session->userdata('user_role') != USER_ROLE_ADMIN)
		{
			redirect('/home');
		}

		$this->load->helper('form_helper');
		$this->load->model('user_model');

		$this->ajax = (bool) $this->input->post('ajax');
	}

	function index()
	{
		$data = array(
			'users' => $this->user_model->listing()
		);

		$this->load->view('header');
		$this->load->view('users/listing', $data);
		$this->load->view('footer');
	}

	function role()
	{
		$user_id = $this->uri->segment(3);

		$new_role = $this->user_model->toggle_role($user_id);

		if ($this->ajax)
		{
			echo json_encode(array('status' => 1, 'content' => $this->user_model->user_role[$new_role]));
		}
		else
		{
			redirect('users');
		}
	}

	function status()
	{
		$user_id = $this->uri->segment(3);
		
		$new_status = $this->user_model->toggle_status($user_id);

		if ($this->ajax)
		{
			echo json_encode(array('status' => 1, 'content' => $this->user_model->user_status[$new_status]));
		}
		else
		{
			redirect('users');
		}
	}

	function edit()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');
		
		if ($this->input->post('save'))
		{
			$this->form_validation->set_rules('name', 'Name', 'required|min_length[3]|max_length[50]|xss_clean');
			$this->form_validation->set_rules('email', 'E-Mailadresse', 'required|valid_email');
			$this->form_validation->set_rules('student_id', 'Matrikelnummer', 'required|exact_length[6]|integer');
			$this->form_validation->set_rules('semester', 'Semester', 'required|callback__semester_check');

			if ($this->form_validation->run())
			{
				// Benutzer bearbeiten
				$this->user_model->edit($this->uri->segment(3), array(
					'user_name' => $this->input->post('name'),
					'user_email' => $this->input->post('email'),
					'user_student_id' => $this->input->post('student_id'),
					'semester_id' => $this->input->post('semester')
				));

				if ($this->input->post('reset_password'))
				{
					/**
					 * @todo Reset password
					 */
				}

				if ($this->ajax)
				{
					// Wird per AJAX aufgerufen => JSON zurückgeben
					echo json_encode(array('status' => 1));
					return;
				}
				else
				{
					// Weiterleiten zur Auflistung
					redirect('users');
				}
			}
		}
		else if ($this->input->post('cancel'))
		{
			redirect('users');
		}

		// Daten fürs View
		$data = array(
			'semesters' => $this->user_model->semester(),
			'user' => $this->user_model->get($this->uri->segment(3)),
			'form_url' => 'users/edit/' . $this->uri->segment(3)
		);

		if ($this->ajax)
		{
			// View wird per AJAX aufgerufen => JSON zurückgeben
			echo json_encode(array('status' => 1, 'content' => $this->load->view('users/edit', $data, true)));
		}
		else
		{
			$this->load->view('header');
			$this->load->view('users/edit', $data);
			$this->load->view('footer');
		}
	}

	function delete()
	{
		if ($this->input->post('delete'))
		{
			// Benutzer aus der Datenbank löschen
			$this->user_model->delete($this->uri->segment(3));

			if ($this->ajax)
			{
				// Wird per AJAX aufgerufen => JSON zurückgeben
				echo json_encode(array('status' => 1));
			}
			else
			{
				// Weiterleiten zur Auflistung
				redirect('users');
			}
		}
		else if ($this->input->post('cancel'))
		{
			redirect('users');
		}
		else
		{
			// Daten fürs View
			$data = array(
				'form_url' => 'users/delete/' . $this->uri->segment(3)
			);

			if ($this->ajax)
			{
				// View wird per AJAX aufgerufen => JSON zurückgeben
				echo json_encode(array('status' => 1, 'content' => $this->load->view('users/delete', $data, true)));
			}
			else
			{
				$this->load->view('header');
				$this->load->view('users/delete', $data);
				$this->load->view('footer');
			}
		}
	}

	function _semester_check($semester)
	{
		return (array_key_exists($semester, $this->user_model->semester()));
	}
}

?>