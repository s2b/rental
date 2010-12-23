<?php

class User_model extends MY_Model
{
	var $table = 'users';

	var $start = '';
	var $limit = '';

	var $user_status = array(
		USER_ACTIVE => 'aktiv',
		USER_INACTIVE => 'inaktiv'
	);
	var $user_role = array(
		USER_ROLE_USER => 'Benutzer',
		USER_ROLE_ADMIN => 'Administrator'
	);

	function listing($status = null)
	{
		if ($status === USER_ACTIVE || $status === USER_INACTIVE)
		{
			$this->db->where('user_status', $status);
		}

		$this->db->order_by('user_reg_date', 'desc');
		$this->_limit();

		$query = $this->db->get($this->table);

		$users = array();
		foreach ($query->result() as $data)
		{
			$user = new stdClass();
			$user->id = $data->user_id;
			$user->status = $data->user_status;
			$user->status_text = $this->user_status[$user->status];
			$user->role = $data->user_role;
			$user->role_text = $this->user_role[$user->role];
			$user->name = $data->user_name;
			$user->email = $data->user_email;
			$user->semester_id = $data->semester_id;
			$user->reg_date = $data->user_reg_date;
			$user->last_visit = $data->user_last_visit;
			$user->student_id = $data->user_student_id;

			$users[] = $user;
		}
		$query->free_result();

		return $users;
	}

	function check($id)
	{
		$this->db->select('user_id');
		$this->db->where('user_id', $id);
		$query = $this->db->get($this->table);

		$result = $query->result();
		$query->free_result();

		return (!empty($result));
	}

	function get($id)
	{
		$this->db->where('user_id', $id);
		$query = $this->db->get($this->table);

		$result = $query->result();
		$query->free_result();

		if (empty($result))
		{
			return false;
		}

		$user = new stdClass();
		$user->name = $result[0]->user_name;
		$user->semester_id = $result[0]->semester_id;
		$user->email = $result[0]->user_email;
		$user->student_id = $result[0]->user_student_id;

		return $user;
	}

	function semester($id = null)
	{
		static $semesters;
		if (!isset($semesters))
		{
			$query = $this->db->get('semesters');

			$semesters = array();
			foreach ($query->result() as $data)
			{
				$semesters[$data->semester_id] = $data->semester_title;
			}
		}

		if (isset($id))
		{
			return (isset($semesters[$id])) ? $semesters[$id] : '';
		}
		else
		{
			return $semesters;
		}
	}

	function add($info)
	{
		if (!isset($info['user_reg_date']))
		{
			$info['user_reg_date'] = date('Y-m-d H:i:s');
		}

		$info['user_id'] = null;
		$info['user_status'] = USER_INACTIVE;
		$info['user_role'] = USER_ROLE_USER;

		$this->db->insert($this->table, $info);
		return $this->db->insert_id();
	}

	function toggle_status($id)
	{
		$this->db->where('user_id', $id);
		$this->db->select('user_status');
		$query = $this->db->get($this->table);

		$result = $query->result();
		$query->free_result();

		$status = ($result[0]->user_status == USER_ACTIVE) ? USER_INACTIVE : USER_ACTIVE;
		$this->edit($id, array('user_status' => (int) $status));

		return $status;
	}

	function toggle_role($id)
	{
		$this->db->where('user_id', $id);
		$this->db->select('user_role');
		$query = $this->db->get($this->table);

		$result = $query->result();
		$query->free_result();

		$role = ($result[0]->user_role == USER_ROLE_ADMIN) ? USER_ROLE_USER : USER_ROLE_ADMIN;
		$this->edit($id, array('user_role' => (int) $role));

		return $role;
	}

	function edit($id, $info)
	{
		$this->db->where('user_id', $id);
		$this->db->update($this->table, $info);
	}

	function delete($id)
	{
		$this->db->where('user_id', $id);
		$this->db->delete($this->table);
	}
}

?>