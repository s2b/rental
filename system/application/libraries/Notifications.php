<?php

class Notifications
{
	var $subject;
	var $message;

	var $adminMails;
	var $systemMail;
	var $systemURL;
	
	var $debugMail;
	
	function Notifications()
	{
		$this->CI =& get_instance();
		
		$this->CI->load->database();
		
		$this->CI->config->load('notifications');
		$this->systemMail = $this->CI->config->item('system_email');
		$this->systemURL = base_url();

		$this->CI->load->library('email');
	}

	function send($email = null)
	{
		if (!$this->CI->config->item('notifications'))
		{
			return;
		}
		
		if (!isset($email))
		{
			$this->CI->load->model('user_model');
			$email = $this->CI->user_model->get_admin_emails();
		}
		else if (is_numeric($email))
		{
			$this->CI->load->model('user_model');
			$email = $this->CI->user_model->get_email($email);
		}
		
		if (empty($email))
		{
			return;
		}

		$this->CI->email->from($this->systemMail);
		$this->CI->email->to($email);
		if (isset($this->debugMail))
		{
			$this->CI->email->cc($this->debugMail);
		}
		$this->CI->email->subject('Rental tool - ' . $this->subject);
		$this->CI->email->message($this->message . "\n\n------\nRental tool\n" . $this->systemURL);
		
		$this->CI->email->send();
		$this->CI->email->clear();
	}

	// nach der Registrierung
	function userRegistration($email)
	{
		$this->subject = 'Erfolgreich registriert';
		$this->message = "Du hast dich erfolgreich registriert.\nSobald dein Benutzerkonto freigegeben wurde, erhältst du eine Bestätigungsemail von uns.";

		$this->send($email);

		$this->subject = 'Neuer Benutzer';
		$this->message = 'Ein neuer Benutzer hat sich registriert und muss durch einen Administrator freigeschaltet werden.';

		$this->send();
	}

	// bei der Aktivierung eines Accounts
	function userEnabled($email)
	{
		$this->subject = 'Benutzerkonto freigegeben';
		$this->message = "Dein Benutzerkonto wurde soeben freigegeben.\nDu kannst dich jetzt anmelden und Inventar buchen bzw. das Studio reservieren.";

		$this->send($email);
	}

	// bei der "Passwort vergessen"-Funktion
	function userForgotPassword($email, $url)
	{
		$this->subject = 'Passwort vergessen';
		$this->message = sprintf("Für dein Benutzerkonto wurde ein neues Passwort angefordert. Bitte klicke auf den folgenden Link, um ein neues Passwort einzugeben:\n\n%s\n\nHinweis: Der Link verliert nach einer Stunde seine Gültigkeit.", $url);

		$this->send($email);
	}

	// bei der Deaktivierung eines Accounts
	function userDisabled($email)
	{
		$this->subject = 'Benutzerkonto gesperrt';
		$this->message = "Dein Benutzerkonto wurde soeben gesperrt.\nBitte wende dich an einen Administrator, wenn du Fragen zur Deaktivierung hast.";

		$this->send($email);
	}

	// beim Inventar buchen
	function inventoryBooking($email)
	{
		$this->subject = 'Neue Inventarbuchung angelegt';
		$this->message = 'Deine Inventarbuchung wurde angelegt. Wenn sie bearbeitet wurde, wirst du darüber per E-Mail informiert.';

		$this->send($email);

		$this->subject = 'Neue Inventarbuchung';
		$this->message = 'Ein Benutzer hat eine neue Inventarbuchung angelegt, die durch einen Administrator bearbeitet werden muss.';

		$this->send();
	}

	// wenn die Inventarbuchung bestätigt wurde
	function inventoryBookingAccepted($email)
	{
		$this->subject = 'Inventarbuchung bestätigt';
		$this->message = 'Deine Inventarbuchung wurde bestätigt. Bitte wende dich an den Verantwortlichen, um einen Termin zur Übergabe zu vereinbaren.';

		$this->send($email);
	}

	// wenn die Inventarbuchung abgelehnt wurde
	function inventoryBookingDenied($email)
	{
		$this->subject = 'Inventarbuchung abgelehnt';
		$this->message = 'Deine Inventarbuchung wurde abgelehnt. Bitte wende dich an den Verantwortlichen, wenn du Fragen zur Ablehnung hast.';

		$this->send($email);
	}

	// beim Studio reservieren
	function studioBooking($email)
	{
		$this->subject = 'Neue Studio-Reservierung angelegt';
		$this->message = 'Deine Studio-Reservierung wurde angelegt. Wenn sie bearbeitet wurde, wirst du darüber per E-Mail informiert.';

		$this->send($email);

		$this->subject = 'Neue Studio-Reservierung';
		$this->message = 'Ein Benutzer hat eine neue Studio-Reservierung angelegt, die durch einen Administrator bearbeitet werden muss.';

		$this->send();
	}

	// wenn die Studio-Reservierung bestätigt wurde
	function studioBookingAccepted($email)
	{
		$this->subject = 'Studio-Reservierung bestätigt';
		$this->message = 'Deine Studio-Reservierung wurde bestätigt. Bitte wende dich an den Verantwortlichen, um einen Termin zur Übergabe zu vereinbaren.';

		$this->send($email);
	}

	// wenn die Studio-Reservierung abgelehnt wurde
	function studioBookingDenied($email)
	{
		$this->subject = 'Studio-Reservierung abgelehnt';
		$this->message = 'Deine Studio-Reservierung wurde abgelehnt. Bitte wende dich an den Verantwortlichen, wenn du Fragen zur Ablehnung hast.';

		$this->send($email);
	}

	// wenn ein Benutzer zum Administrator gemacht wird
	function newAdministrator()
	{
		$this->subject = 'Neuer Administrator';
		$this->message = 'Es gibt einen neuen Administrator im Ausleihtool.';

		$this->send();
	}
}