<?php

class Inventory extends Controller
{
	function Inventory()
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
		$this->load->model('inventory_model');

		$this->ajax = (bool) $this->input->post('ajax');
	}

	function index()
	{
		$data = array(
			'inventory' => $this->inventory_model->listing()
		);

		$this->load->view('header');
		$this->load->view('inventory/listing', $data);
		$this->load->view('footer');
	}

	function add()
	{
		$inv = new stdClass();
		$inv->title = '';
		$inv->desc = '';

		$this->_add_edit('add', $inv);
	}

	function status()
	{
		if (!$this->inventory_model->check($this->uri->segment(3)))
		{
			return;
		}

		$inventory_id = $this->uri->segment(3);

		$new_status = $this->inventory_model->toggle_status($inventory_id);

		if ($this->ajax)
		{
			echo json_encode(array('status' => 1, 'content' => $this->inventory_model->inventory_status[$new_status]));
		}
		else
		{
			redirect('inventory');
		}
	}

	function edit()
	{
		$inv = $this->inventory_model->get($this->uri->segment(3));
		if (!$inv)
		{
			return;
		}

		$this->_add_edit('edit', $inv);
	}

	function _add_edit($mode, $inv)
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
					'inventory_title' => $this->input->post('title'),
					'inventory_desc' => $this->input->post('desc')
				);

				if ($mode == 'add')
				{
					// Inventar hinzufügen
					$this->inventory_model->add($data);
				}
				else
				{
					// Inventar bearbeiten
					$this->inventory_model->edit($this->uri->segment(3), $data);
				}

				// Weiterleiten zur Auflistung
				redirect('inventory');
			}
		}
		else if ($this->input->post('cancel'))
		{
			redirect('inventory');
		}

		// Daten fürs View
		$data = array(
			'mode' => $mode,
			'inv' => $inv,
			'form_url' => ($mode == 'add') ? 'inventory/add/' . $this->uri->segment(3) : 'inventory/edit/' . $this->uri->segment(3)
		);

		if ($this->ajax)
		{
			// View wird per AJAX aufgerufen => JSON zurückgeben
			echo json_encode(array('status' => 1, 'content' => $this->load->view('inventory/add_edit', $data, true)));
		}
		else
		{
			$this->load->view('header');
			$this->load->view('inventory/add_edit', $data);
			$this->load->view('footer');
		}
	}

	function delete()
	{
		if (!$this->inventory_model->check($this->uri->segment(3)))
		{
			return;
		}
		
		if ($this->input->post('delete'))
		{
			// Inventar aus der Datenbank löschen
			$this->inventory_model->delete($this->uri->segment(3));

			// Weiterleiten zur Auflistung
			redirect('inventory');
		}
		else if ($this->input->post('cancel'))
		{
			redirect('inventory');
		}
		else
		{
			// Daten fürs View
			$data = array(
				'form_url' => 'inventory/delete/' . $this->uri->segment(3)
			);

			if ($this->ajax)
			{
				// View wird per AJAX aufgerufen => JSON zurückgeben
				echo json_encode(array('status' => 1, 'content' => $this->load->view('inventory/delete', $data, true)));
			}
			else
			{
				$this->load->view('header');
				$this->load->view('inventory/delete', $data);
				$this->load->view('footer');
			}
		}
	}
}

?>