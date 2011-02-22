<?php

class Packages_model extends MY_Model
{
	var $table = 'packages';

	var $start = '';
	var $limit = '';
	var $order = 'package_time desc';

	var $package_status = array(
		PACKAGE_ACTIVE => 'aktiv',
		PACKAGE_INACTIVE => 'inaktiv'
	);

	function listing($status = null, $inventory = array())
	{
		$this->db->select('packages.*, packages_inventory.*, users.user_name, users.user_email, users.semester_id');
		$this->db->join('users', 'users.user_id = packages.user_id');
		$this->db->join('packages_inventory', 'packages_inventory.package_id = packages.package_id');

		if ($status === PACKAGE_ACTIVE || $status === PACKAGE_INACTIVE)
		{
			$this->db->where('package_status', $status);
		}

		$this->_order();
		$this->_limit();

		$query = $this->db->get($this->table);

		$packages = array();
		foreach ($query->result() as $data)
		{
			if (!isset($packages[$data->package_id]))
			{
				$package = new stdClass();
				$package->id = $data->package_id;
				$package->status = $data->package_status;
				$package->status_text = $this->package_status[$package->status];
				$package->time = $data->package_time;
				$package->title = $data->package_title;
				$package->desc = $data->package_desc;
	
				$package->user = new stdClass();
				$package->user->id = $data->user_id;
				$package->user->name = $data->user_name;
				$package->user->email = $data->user_email;
				$package->user->semester_id = $data->semester_id;
				
				$package->inventory = array();

				$packages[$package->id] = $package;
			}
			
			if (isset($inventory[$data->inventory_id]))
			{
				$packages[$package->id]->inventory[$data->inventory_id] = $inventory[$data->inventory_id];
			}
		}
		$query->free_result();

		return $packages;
	}

	function check($id)
	{
		$this->db->select('package_id');
		$this->db->where('package_id', $id);
		$query = $this->db->get($this->table);

		$result = $query->result();
		$query->free_result();

		return (!empty($result));
	}

	function get($id)
	{
		$this->db->join('packages_inventory', 'packages_inventory.package_id = packages.package_id');
		$this->db->where('packages.package_id', $id);
		$query = $this->db->get($this->table);

		foreach ($query->result() as $item)
		{
			if (!isset($package))
			{
				$package = new stdClass();
				$package->title = $item->package_title;
				$package->desc = $item->package_desc;
				$package->inventory = array();
			}
			
			$package->inventory[] = $item->inventory_id;
		}
		$query->free_result();

		if (!isset($package))
		{
			return false;
		}

		return $package;
	}

	function add($info)
	{
		$CI =& get_instance();
		
		$inventory = (isset($info['inventory'])) ? (array) $info['inventory'] : null;
		unset($info['inventory']);
		
		if (!isset($info['package_time']))
		{
			$info['package_time'] = date('Y-m-d H:i:s');
		}

		if (!isset($info['user_id']))
		{
			$info['user_id'] = $CI->session->userdata['user_id'];
		}

		$info['package_id'] = null;
		$info['package_status'] = PACKAGE_ACTIVE;

		$this->db->insert($this->table, $info);
		$package_id = $this->db->insert_id();
		
		if (isset($inventory))
		{
			foreach ($inventory as $item)
			{
				$this->db->insert('packages_inventory', array(
					'package_id' => $package_id,
					'inventory_id' => (int) $item));
			}
		}
		
		return $package_id;
	}

	function toggle_status($id)
	{
		$this->db->where('package_id', $id);
		$this->db->select('package_status');
		$query = $this->db->get($this->table);

		$result = $query->result();
		$query->free_result();

		$status = ($result[0]->package_status == PACKAGE_ACTIVE) ? PACKAGE_INACTIVE : PACKAGE_ACTIVE;
		$this->edit($id, array('package_status' => (int) $status));

		return $status;
	}

	function edit($id, $info)
	{
		$CI =& get_instance();
		
		$inventory = (isset($info['inventory'])) ? (array) $info['inventory'] : null;
		unset($info['inventory']);

		$info['package_time'] = date('Y-m-d H:i:s');
		$info['user_id'] = $CI->session->userdata['user_id'];

		$this->db->where('package_id', $id);
		$this->db->update($this->table, $info);
		
		if (isset($inventory))
		{
			$this->db->delete('packages_inventory', array('package_id' => $id));
			foreach ($inventory as $item)
			{
				$this->db->insert('packages_inventory', array(
					'package_id' => $id,
					'inventory_id' => (int) $item));
			}
		}
	}

	function delete($id)
	{
		$this->db->delete('packages_inventory', array('package_id' => $id));
		$this->db->delete($this->table, array('package_id' => $id));
	}

}

?>