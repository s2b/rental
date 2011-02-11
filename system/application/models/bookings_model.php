<?php

class Bookings_model extends MY_Model
{
	var $table = 'bookings';

	var $start = '';
	var $limit = '';

	var $status_text = array(
		BOOKING_NEW => 'angefragt',
		BOOKING_CONFIRMED => 'bestätigt',
		BOOKING_BORROWED => 'ausgeliehen',
		BOOKING_CLOSED => 'abgeschlossen',
		BOOKING_DENIED => 'abgelehnt');

	function listing($is_room = false, $updates = false, $own_listing = false)
	{
		$where_sql = array();
		if (isset($is_room))
		{
			$where_sql[] = 'b.booking_room = ' . (int) $is_room;
		}
		
		if ($own_listing)
		{
			$where_sql[] = 'b.user_id = ' . (int) $this->session->userdata('user_id');
		}
		
		$where_sql = (!empty($where_sql)) ? 'WHERE ' . implode(' AND ', $where_sql) : '';
		$sql = 'SELECT b.booking_id, b.booking_status, b.booking_time, b.booking_start, b.booking_end,
					b.booking_desc, b.user_id AS booking_user_id, u_b.user_name AS booking_user_name,
					u_b.user_email AS booking_user_email, u_b.semester_id AS booking_semester_id,
				i.inventory_id, i.inventory_status, i.inventory_title, i.inventory_desc
			FROM bookings b
			INNER JOIN bookings_inventory bi
			   ON bi.booking_id = b.booking_id
			INNER JOIN users u_b
				ON u_b.user_id = b.user_id
			INNER JOIN inventory i
			   ON i.inventory_id = bi.inventory_id
			' . $where_sql . '
			ORDER BY b.booking_status, b.booking_time DESC';
		$query = $this->db->query($sql);

		$bookings = array();
		foreach ($query->result() as $data)
		{
			$id = $data->booking_id;
			if (!isset($bookings[$id]))
			{
				$bookings[$id] = new stdClass();

				$bookings[$id]->id = $id;
				$bookings[$id]->status = $data->booking_status;
				$bookings[$id]->status_text = $this->status_text[$data->booking_status];
				$bookings[$id]->time = $data->booking_time;
				$bookings[$id]->start = $data->booking_start;
				$bookings[$id]->end = $data->booking_end;
				$bookings[$id]->desc = $data->booking_desc;

				$bookings[$id]->user = new stdClass();
				$bookings[$id]->user->id = $data->booking_user_id;
				$bookings[$id]->user->name = $data->booking_user_name;
				$bookings[$id]->user->email = $data->booking_user_email;
				$bookings[$id]->user->semester_id = $data->booking_semester_id;

				$bookings[$id]->inventory = $bookings[$id]->updates = array();
			}

			$inv_id = $data->inventory_id;
			$bookings[$id]->inventory[$inv_id] = new stdClass();
			$bookings[$id]->inventory[$inv_id]->id = $inv_id;
			$bookings[$id]->inventory[$inv_id]->status = $data->inventory_status;
			$bookings[$id]->inventory[$inv_id]->title = $data->inventory_title;
			$bookings[$id]->inventory[$inv_id]->desc = $data->inventory_desc;
		}
		$query->free_result();

		if ($updates && !empty($bookings))
		{
			foreach ($this->getUpdates(array_keys($bookings)) as $id => $updates)
			{
				$bookings[$id]->updates = $updates;
			}
		}

		return $bookings;
	}

	function getUpdates($ids)
	{
		$updates = array();
		foreach ($ids as $id)
		{
			$updates[(int) $id] = array();
		}

		$sql = 'SELECT bu.booking_id, bu.booking_status, bu.update_time, bu.update_desc, bu.user_id,
				u_bu.user_name, u_bu.user_email, u_bu.semester_id
			FROM bookings_updates bu
			INNER JOIN users u_bu
				ON u_bu.user_id = bu.user_id
			WHERE bu.booking_id IN(' . implode(', ', array_keys($updates)) . ')
			ORDER BY bu.update_time';
		$query = $this->db->query($sql);

		foreach ($query->result() as $data)
		{
			$update = new stdClass();
			$update->status = $data->booking_status;
			$update->status_text = $this->status_text[$data->booking_status];
			$update->time = $data->update_time;
			$update->desc = $data->update_desc;

			$update->user = new stdClass();
			$update->user->id = $data->user_id;
			$update->user->name = $data->user_name;
			$update->user->email = $data->user_email;
			$update->user->semester_id = $data->semester_id;

			$updates[$data->booking_id][] = $update;
		}
		$query->free_result();

		return $updates;
	}

	function check($id)
	{
		$this->db->select('booking_id');
		$this->db->where('booking_id', $id);
		$query = $this->db->get($this->table);

		$result = $query->result();
		$query->free_result();

		return (!empty($result));
	}

	function add($start, $end, $desc, $is_room = false)
	{
		$data = array(
			'user_id' => $this->session->userdata('user_id'),
			'booking_status' => BOOKING_NEW,
			'booking_room' => (int) $is_room,
			'booking_time' => date('Y-m-d H:i:s'),
			'booking_start' => $start,
			'booking_end' => $end,
			'booking_desc' => $desc
		);

		$this->db->insert($this->table, $data);

		return $this->db->insert_id();
	}

	function delete($id)
	{
		$this->db->delete('bookings_inventory', array('booking_id' => $id));
		$this->db->delete('bookings_updates', array('booking_id' => $id));
		$this->db->delete($this->table, array('booking_id' => $id));
	}

	function updateStatus($id, $status, $desc)
	{
		if (!isset($this->status_text[$status]))
		{
			return;
		}

		$data = array(
			'booking_id' => $id,
			'user_id' => $this->session->userdata('user_id'),
			'booking_status' => $status,
			'update_time' => date('Y-m-d H:i:s'),
			'update_desc' => $desc
		);

		$this->db->insert('bookings_updates', $data);

		$this->db->where('booking_id', $id);
		$this->db->update($this->table, array(
			'booking_status' => $status
		));
	}
}

?>