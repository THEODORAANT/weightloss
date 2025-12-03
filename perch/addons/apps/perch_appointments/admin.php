<?php
   	if ($CurrentUser->logged_in() && $CurrentUser->has_priv('perch_appointments')) {
   		$this->register_app('perch_appointments', 'Appointments', 1, 'Appointments', '1.0');
    	$this->require_version('perch_appointments', '1.0');
	    $this->add_create_page('perch_appointments', 'edit');
	}


	spl_autoload_register(function($class_name){
    	if (strpos($class_name, 'PerchAppointments')===0) {
    		include(PERCH_PATH.'/addons/apps/perch_appointments/lib/'.$class_name.'.class.php');
    		return true;
    	}

    	return false;
    });
