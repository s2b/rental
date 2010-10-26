<?php

class User_model extends MY_Model
{
	var $table = 'users';

	var $start = '';
	var $limit = '';

	function listing($status = null)
	{
		$this->db->from($this->table);

		if ($status == USER_ACTIVE || $status == USER_INACTIVE)
		{
			$this->db->where('status', $status);
		}

		$this->db->order_by('user_reg_date', 'desc');
		$this->_limit();

		$query = $this->db->get();

		return $query->result();
	}

	function semester($id)
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

		return (isset($semesters[$id])) ? $semesters[$id] : '';
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