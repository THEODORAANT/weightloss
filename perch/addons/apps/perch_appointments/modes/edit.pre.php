<?php
    $Appointments = new PerchAppointments_Appointments($API);
        $appointmentID = false;

	$edit_mode  	= false;
	$Appointment    	= false;
	$message		= false;
	$details 		= false;

	if (PerchUtil::get('id')) {

		if (!$CurrentUser->has_priv('perch_appointments.appointments.edit')) {
		    PerchUtil::redirect($API->app_path());
		}

		$announcementID = PerchUtil::get('id');
		$Appointment   = $Appointments->find($appointmentID);
		 $details  = $Appointment->to_array();
		$edit_mode         = true;

	}else{
		if (!$CurrentUser->has_priv('perch_appointments.appointments.create')) {
		    PerchUtil::redirect($API->app_path());
		}
	}

	// Template
	$Template   = $API->get('Template');


	$Template->set('appointments/appointments.html', 'appointments');


	$tags = $Template->find_all_tags_and_repeaters();

	$Form = $API->get('Form');
	$Form->handle_empty_block_generation($Template);

	$Form->set_required_fields_from_template($Template, $details);


	if ($Form->submitted()) {

       $prev = false;

        if (isset($details['announcementDynamicFields'])) {
            $prev = PerchUtil::json_safe_decode($details['announcementDynamicFields'], true);
        }

    	$dynamic_fields = $Form->receive_from_template_fields($Template, $prev, $Announcements, $Announcement, $clear_post=true, $strip_static_fields=false);

        PerchUtil::debug('Dynamic fields:');
        PerchUtil::debug($dynamic_fields);


          // fetch out static fields
                if (isset($dynamic_fields['announcementContent']) && is_array($dynamic_fields['announcementContent'])) {
                    $data['announcementContentRaw']  = $dynamic_fields['announcementContent']['raw'];
                    $data['announcementContent'] = $dynamic_fields['announcementContent']['processed'];
                    unset($dynamic_fields['announcementContent']);
                }

    	$data['announcementCreatedDate'] =date("Y-m-d H:i:s") ;
$data['announcementTitle'] = $dynamic_fields['announcementTitle'];
		if (is_object($Announcement)) {
			$Announcement->update($data);

		}else{

			$Appointment = $Appointments->create($data);

			if ($Announcement) {
				//$Listing->index($Template);
				//$Listing->update_search_text($search_text);
				PerchUtil::redirect($Perch->get_page().'?id='.$Appointment->id().'&created=1');
			}

		}

		if (is_object($Appointment)) {
		    $message = $HTML->success_message('Your Announcement has been successfully edited. Return to %sAnnouncement%s', '<a href="'.$API->app_path('perch_announcements') .'" class="notification-link">', '</a>');
		}else{
		    $message = $HTML->failure_message('Sorry, that update was not successful.');
		}

	}



	if (PerchUtil::get('created') && !$message) {
	    $message = $HTML->success_message('Your listing has been successfully created. Return to %slisting listing%s', '<a href="'. $API->app_path('perch_listings') .'" class="notification-link">', '</a>');
	}

	if (is_object($Announcement)) {
		$details = $Appointment->to_array();
	}

