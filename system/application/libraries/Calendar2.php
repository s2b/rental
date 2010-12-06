<?php

class Calendar2
{
	var $view = 'calendar';
	var $first_weekday = 1;
	var $weekday_display = 2;
	var $month_display = 1;
	var $show_next_prev = true;
	var $next_prev_url = '';

	var $month;
	var $year;
	var $current;
	var $dates = array();

	var $weekdays = array(
		array('cal_su', 'cal_sun', 'cal_sunday'),
		array('cal_mo', 'cal_mon', 'cal_monday'),
		array('cal_tu', 'cal_tue', 'cal_tuesday'),
		array('cal_we', 'cal_wed', 'cal_wednesday'),
		array('cal_th', 'cal_thu', 'cal_thursday'),
		array('cal_fr', 'cal_fri', 'cal_friday'),
		array('cal_sa', 'cal_sat', 'cal_saturday')
	);
	var $months = array(
		1 => array('cal_jan', 'cal_january'),
		2 => array('cal_feb', 'cal_february'),
		3 => array('cal_mar', 'cal_march'),
		4 => array('cal_apr', 'cal_april'),
		5 => array('cal_may', 'cal_mayl'),
		6 => array('cal_jun', 'cal_june'),
		7 => array('cal_jul', 'cal_july'),
		8 => array('cal_aug', 'cal_august'),
		9 => array('cal_sep', 'cal_september'),
		10 => array('cal_oct', 'cal_october'),
		11 => array('cal_nov', 'cal_november'),
		12 => array('cal_dec', 'cal_december')
	);

	function Calendar2($config = array())
	{
		$this->CI =& get_instance();
		$this->CI->load->helper('date');

		if (!in_array('calendar_lang'.EXT, $this->CI->lang->is_loaded, TRUE))
		{
			$this->CI->lang->load('calendar');
		}

		if (!in_array('my_calendar_lang'.EXT, $this->CI->lang->is_loaded, TRUE))
		{
			$this->CI->lang->load('my_calendar');
		}

		$this->current = explode('-', date('Y-m-d'));
		$this->select_month();

		$this->initialize($config);
	}

	function initialize($config)
	{
		if (isset($config['first_weekday']))
		{
			$weekdays = array_flip(array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'));
			if (isset($weekdays[$config['first_weekday']]))
			{
				$this->first_weekday = $weekdays[$config['first_weekday']];
			}
		}

		if (isset($config['view']))
		{
			$this->view = $config['view'];
		}

		if (isset($config['weekday_display']))
		{
			switch ($config['weekday_display'])
			{
				case 'Mo':
					$this->weekday_display = 0;
				break;

				case 'Mon':
					$this->weekday_display = 1;
				break;

				case 'Monday':
					$this->weekday_display = 2;
				break;
			}
		}

		if (isset($config['month_display']))
		{
			switch ($config['month_display'])
			{
				case 'Jan':
					$this->month_display = 0;
				break;

				case 'January':
					$this->month_display = 1;
				break;
			}
		}

		$this->show_next_prev = (!empty($config['show_next_prev']));
		$this->next_prev_url = (isset($config['next_prev_url'])) ? $config['next_prev_url'] : '';
	}

	function select_month($month = null, $year = null)
	{
		if (!isset($month))
		{
			$month = $this->current[1];
		}

		if (!isset($year))
		{
			$year = $this->current[0];
		}

		$this->month = (int) $month;
		$this->year = (int) $year;

		$days = days_in_month($this->month, $this->year);
		$this->dates = array_fill(1, $days, array());
	}

	function add_date($day, $start = null, $end = null, $desc = '', $data = null)
	{
		$day = (int) $day;
		if (!isset($this->dates[$day]))
		{
			return;
		}

		if (isset($start))
		{
			$timeframe = (isset($end)) ? "$start – $end" : sprintf($this->CI->lang->line('timeframe_from'), $start);
		}
		else
		{
			$timeframe = (isset($end)) ? sprintf($this->CI->lang->line('timeframe_to'), $end) : '';
			$start = '0:00';
		}

		if (!isset($end))
		{
			$end = '24:00';
		}

		$this->dates[$day][] = array(
			'start' => $start,
			'end' => $end,
			'timeframe' => $timeframe,
			'desc' => $desc,
			'data' => $data
		);
	}

	function add_date_timestamp($start, $end, $desc = '', $data = null)
	{
		if (date('Y', $start) > $this->year || date('m', $start) > $this->month
			|| date('Y', $end) < $this->year || date('m', $end) < $this->month)
		{
			return;
		}

		if (date('Y-m-d', $start) == date('Y-m-d', $end))
		{
			$this->add_date(date('d', $start), date('G:i', $start), date('G:i', $end), $desc, $data);
			return;
		}

		$temp = $this->year . '-' . $this->month;
		if (date('Y-m', $start) != $temp)
		{
			$start = strtotime("$temp-01 0:00:00");
		}
		if (date('Y-m', $end) != $temp)
		{
			$end = strtotime("$temp-" . days_in_month($this->month, $this->year) . ' 23:59:59');
		}

		$temp = date('G:i', $start);
		if ($temp == '0:00')
		{
			$temp = null;
		}
		$this->add_date(date('d', $start), $temp, null, $desc, $data);

		$min = strtotime(date('Y-m-d', $start) . ' 0:00:00 + 1 day');
		$max = strtotime(date('Y-m-d', $end) . ' 23:59:59 - 1 day');
		for ($i = $min; $i <= $max; $i += 24 * 3600)
		{
			$this->add_date(date('d', $i), null, null, $desc, $data);
		}

		$temp = date('G:i', $end);
		if ($temp == '23:59')
		{
			$temp = null;
		}
		$this->add_date(date('d', $end), null, $temp, $desc, $data);
	}

	function generate()
	{
		$current = ($this->current[0] == $this->year && $this->current[1] == $this->month) ? $this->current[2] : false;

		$first_day = $this->weekday(1);
		$month_days = count($this->dates);
		$day = 1;

		$calendar = array();
		while ($day < $month_days)
		{
			if ($first_day)
			{
				$start = $first_day;
				$first_day = false;
			}
			else
			{
				$start = 0;
			}
			$count = min(6, $month_days - $day);

			$week = array_fill(0, 7, false);
			for ($weekday = $start; $weekday <= $count; $weekday++)
			{
				$week[$weekday] = array(
					'day' => $day,
					'today' => ($day == $current),
					'dates' => $this->dates[$day]);
				$day++;
			}
			
			$calendar[] = $week;
		}

		$days = array();
		for ($i = $this->first_weekday; $i <= 6; $i++)
		{
			$days[] = $this->lang('weekday', $i);
		}
		for ($i = 0; $i < $this->first_weekday; $i++)
		{
			$days[] = $this->lang('weekday', $i);
		}

		$prev_url = ($this->month == 1) ? ($this->year - 1) . '/12' : $this->year . '/' . ($this->month - 1);
		$next_url = ($this->month == 12) ? ($this->year + 1) . '/1' : $this->year . '/' . ($this->month + 1);

		return $this->CI->load->view($this->view, array(
			'calendar' => $calendar,
			'month' => $this->lang('month', $this->month),
			'year' => $this->year,
			'days' => $days,
			'prev_url' => $this->next_prev_url . '/' . $prev_url,
			'next_url' => $this->next_prev_url . '/' . $next_url,
			'show_next_prev' => $this->show_next_prev
		), true);
	}

	function lang($mode, $key)
	{
		switch ($mode)
		{
			case 'month':
				return $this->CI->lang->line($this->months[$key][$this->month_display]);

			case 'weekday':
				return $this->CI->lang->line($this->weekdays[$key][$this->weekday_display]);
		}
	}

	function weekday($day, $as_string = false)
	{
		$weekday = date('w', mktime(0, 0, 0, $this->month, $day, $this->year));

		$weekday = $weekday - $this->first_weekday;
		$weekday = $weekday % 6;

		if ($weekday < 0)
		{
			$weekday = 7 + $weekday;
		}

		return $weekday;
	}
}

?>