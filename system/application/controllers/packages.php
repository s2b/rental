<?php

class Packages extends Controller
{
	function Packages()
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
		$this->load->model('user_model');
		$this->load->model('packages_model');
		$this->load->model('inventory_model');
		
		$this->inventory_model->order = 'inventory_title asc';

		$this->ajax = (bool) $this->input->post('ajax');
	}

	function index()
	{
		$inventory = $this->inventory_model->listing();
		
		$data = array(
			'packages' => $this->packages_model->listing(null, $inventory)
		);

		$this->load->view('header');
		$this->load->view('packages/listing', $data);
		$this->load->view('footer');
	}

	function add()
	{
		$inv = new stdClass();
		$inv->title = '';
		$inv->desc = '';
		$inv->inventory = array();

		$this->_add_edit('add', $inv);
	}

	function status()
	{
		if (!$this->packages_model->check($this->uri->segment(3)))
		{
			return;
		}

		$package_id = $this->uri->segment(3);

		$new_status = $this->packages_model->toggle_status($package_id);

		if ($this->ajax)
		{
			echo json_encode(array('status' => 1, 'content' => $this->packages_model->package_status[$new_status]));
		}
		else
		{
			redirect('packages');
		}
	}

	function edit()
	{
		$package = $this->packages_model->get($this->uri->segment(3));
		if (!$package)
		{
			return;
		}

		$this->_add_edit('edit', $package);
	}

	function _add_edit($mode, $package)
	{
		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('<div class="error">', '</div>');

		if ($this->input->post('save'))
		{
			$this->form_validation->set_rules('title', 'Titel', 'required|min_length[3]|max_length[50]|xss_clean');
			$this->form_validation->set_rules('desc', 'Beschreibung', 'xss_clean');

			if ($this->form_validation->run())
			{
				$data = array(
					'package_title' => $this->input->post('title'),
					'package_desc' => $this->input->post('desc'),
					'inventory' => $this->input->post('inventory')
				);

				if ($mode == 'add')
				{
					// Paket hinzufügen
					$this->packages_model->add($data);
				}
				else
				{
					// Paket bearbeiten
					$this->packages_model->edit($this->uri->segment(3), $data);
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
					redirect('packages');
				}
			}
		}
		else if ($this->input->post('cancel'))
		{
			redirect('packages');
		}

		// Daten fürs View
		$data = array(
			'mode' => $mode,
			'package' => $package,
			'inventory' => $this->inventory_model->listing(),
			'form_url' => ($mode == 'add') ? 'packages/add/' . $this->uri->segment(3) : 'packages/edit/' . $this->uri->segment(3)
		);

		if ($this->ajax)
		{
			// View wird per AJAX aufgerufen => JSON zurückgeben
			echo json_encode(array('status' => 1, 'content' => $this->load->view('packages/add_edit', $data, true)));
		}
		else
		{
			$this->load->view('header');
			$this->load->view('packages/add_edit', $data);
			$this->load->view('footer');
		}
	}

	function delete()
	{
		if (!$this->packages_model->check($this->uri->segment(3)))
		{
			return;
		}
		
		if ($this->input->post('delete'))
		{
			// Paket aus der Datenbank löschen
			$this->packages_model->delete($this->uri->segment(3));

			if ($this->ajax)
			{
				// Wird per AJAX aufgerufen => JSON zurückgeben
				echo json_encode(array('status' => 1));
			}
			else
			{
				// Weiterleiten zur Auflistung
				redirect('packages');
			}
		}
		else if ($this->input->post('cancel'))
		{
			redirect('packages');
		}
		else
		{
			// Daten fürs View
			$data = array(
				'form_url' => 'packages/delete/' . $this->uri->segment(3)
			);

			if ($this->ajax)
			{
				// View wird per AJAX aufgerufen => JSON zurückgeben
				echo json_encode(array('status' => 1, 'content' => $this->load->view('packages/delete', $data, true)));
			}
			else
			{
				$this->load->view('header');
				$this->load->view('packages/delete', $data);
				$this->load->view('footer');
			}
		}
	}
}

?>