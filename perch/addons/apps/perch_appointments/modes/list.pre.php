<?php


    $Appointments = new PerchAppointments_Appointments($API);

    $Paging = $API->get('Paging');
    $Paging->set_per_page(10);

   
    $appointments = array();

    $filter = 'future';
    
    if (isset($_GET['by']) && $_GET['by']!='') {
        $filter = $_GET['by'];
    }

    
    switch ($filter) {
        case 'past':
            $appointments = $Appointments->all($Paging, false);
            break;

        default:
            $appointments = $Appointments->all($Paging);
            
            // Install
            if ($appointments == false) {
                $Appointments->attempt_install();
            }
            
            break;
    }
