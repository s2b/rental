<?php

class Menu_model extends MY_Model
{
	function user_menu()
	{
		return array(
			'home' => array('Übersicht', 1),
			'booking/inventory' => array('Inventar anfragen', 2),
			'booking/studio' => array('Studio anfragen', 2));
	}

	function admin_menu()
	{
		return array(
			'bookings/inventory' => array('Inventarbuchungen', 2),
			'bookings/studio' => array('Studio-Reservierungen', 2),
			'users' => array('Benutzer', 1),
			'inventory' => array('Inventar', 1),
			'packages' => array('Inventar-Pakete', 1));
	}
}

?>