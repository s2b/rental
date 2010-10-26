<?php

class MY_URI extends CI_URI
{
	function uri_part($length)
	{
		$uri_part = array();
		for ($i = 1; $i <= $length; $i++)
		{
			if (isset($this->segments[$i]))
			{
				$uri_part[] = $this->segments[$i];
			}
		}

		return implode('/', $uri_part);
	}

	function ruri_part($length)
	{
		$segments = $this->rsegment_array();

		$uri_part = array();
		for ($i = 1; $i <= $length; $i++)
		{
			if (isset($segments[$i]))
			{
				$uri_part[] = $segments[$i];
			}
		}

		return implode('/', $uri_part);
	}
}

?>