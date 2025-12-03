<?php
    
    $Appointments = new PerchAppointments_Appointments($API);

    $Form = $API->get('Form');

    $Form->set_name('delete');

	
	$message = false;
	
	if (isset($_GET['id']) && $_GET['id']!='') {
	    $Appointment = $Appointments->find($_GET['id']);
	}else{
	    PerchUtil::redirect($API->app_path());
	}
	

    if ($Form->submitted()) {	
    	
    	if (is_object($Appointment)) {
    	    $Appointment->delete();


            if ($Form->submitted_via_ajax) {
                echo $API->app_path().'/';
                exit;
            }else{
               PerchUtil::redirect($API->app_path().'/'); 
            }


            
        }else{
            $message = $HTML->failure_message('Sorry, that Appointment could not be deleted.');
        }
    }

    
    
    $details = $Appointment->to_array();
