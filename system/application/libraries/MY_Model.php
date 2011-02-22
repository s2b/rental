<?php

class MY_Model extends Model
{
	function _limit()
	{
		if ($this->start !== '')
		{
			$this->db->limit($this->start, $this->limit);
		}

		$this->start = '';
		$this->limit = '';
	}
	
	function _order()
	{
		if ($this->order)
		{
			$this->db->order_by($this->order);
		}
	}
}

?>