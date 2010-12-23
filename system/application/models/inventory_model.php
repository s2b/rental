<?php

class Inventory_model extends MY_Model
{
	var $table = 'inventory';

	var $start = '';
	var $limit = '';

	var $inventory_status = array(
		INVENTORY_ACTIVE => 'aktiv',
		INVENTORY_INACTIVE => 'inaktiv'
	);

	function listing($status = null, $is_room = false)
	{
		$this->db->select('inventory.*, users.user_name, users.user_email, users.semester_id');
		$this->db->join('users', 'users.user_id = inventory.user_id');

		if ($status === INVENTORY_ACTIVE || $status === INVENTORY_INACTIVE)
		{
			$this->db->where('inventory_status', $status);
		}

		if (isset($is_room))
		{
			$this->db->where('inventory_room', (int) $is_room);
		}

		$this->db->order_by('inventory_time', 'desc');
		$this->_limit();

		$query = $this->db->get($this->table);

		$inventory = array();
		foreach ($query->result() as $data)
		{
			$inv = new stdClass();
			$inv->id = $data->inventory_id;
			$inv->status = $data->inventory_status;
			$inv->status_text = $this->inventory_status[$inv->status];
			$inv->room = $data->inventory_room;
			$inv->time = $data->inventory_time;
			$inv->title = $data->inventory_title;
			$inv->desc = $data->inventory_desc;

			$inv->user = new stdClass();
			$inv->user->id = $data->user_id;
			$inv->user->name = $data->user_name;
			$inv->user->email = $data->user_email;
			$inv->user->semester_id = $data->semester_id;

			$inventory[] = $inv;
		}
		$query->free_result();

		return $inventory;
	}

	function check($id)
	{
		$this->db->select('inventory_id');
		$this->db->where('inventory_id', $id);
		$query = $this->db->get($this->table);

		$result = $query->result();
		$query->free_result();

		return (!empty($result));
	}

	function get($id, $is_room = false)
	{
		if (isset($is_room))
		{
			$this->db->where('inventory_room', (int) $is_room);
		}
		
		$this->db->where('inventory_id', $id);
		$query = $this->db->get($this->table);

		$result = $query->result();
		$query->free_result();

		if (empty($result))
		{
			return false;
		}

		$inv = new stdClass();
		$inv->title = $result[0]->inventory_title;
		$inv->desc = $result[0]->inventory_desc;

		return $inv;
	}

	function add($info)
	{
		$CI =& get_instance();
		
		if (!isset($info['inventory_time']))
		{
			$info['inventory_time'] = date('Y-m-d H:i:s');
		}

		if (!isset($info['user_id']))
		{
			$info['user_id'] = $CI->session->userdata['user_id'];
		}

		if (!isset($info['inventory_room']))
		{
			$info['inventory_room'] = 0;
		}

		$info['inventory_id'] = null;
		$info['inventory_status'] = INVENTORY_ACTIVE;

		$this->db->insert($this->table, $info);
		return $this->db->insert_id();
	}

	function toggle_status($id)
	{
		$this->db->where('inventory_id', $id);
		$this->db->select('inventory_status');
		$query = $this->db->get($this->table);

		$result = $query->result();
		$query->free_result();

		$status = ($result[0]->inventory_status == INVENTORY_ACTIVE) ? INVENTORY_INACTIVE : INVENTORY_ACTIVE;
		$this->edit($id, array('inventory_status' => (int) $status));

		return $status;
	}

	function edit($id, $info)
	{
		$CI =& get_instance();

		$info['inventory_time'] = date('Y-m-d H:i:s');
		$info['user_id'] = $CI->session->userdata['user_id'];

		$this->db->where('inventory_id', $id);
		$this->db->update($this->table, $info);
	}

	function delete($id)
	{
		$this->db->where('inventory_id', $id);
		$this->db->delete($this->table);
	}

}

?>