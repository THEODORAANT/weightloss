<?php

class PerchAppointment_Runtime
{
	private static $instance;

	private $api                  = null;

	private $appointmentID           = null;

	


	public static function fetch()
	{
		if (!isset(self::$instance)) self::$instance = new PerchAppointment_Runtime;
        return self::$instance;
	}

	public function __construct()
	{
		$this->api = new PerchAPI(1.0, 'perch_appointments');

	}






}
