<?php

$Appointments = new PerchAppointments_Appointments($API);
$Appointments->ensure_schema();

$appointmentID = false;
$Appointment = false;
$message = false;
$details = false;

if (PerchUtil::get('id')) {
    $appointmentID = (int) PerchUtil::get('id');
    $Appointment = $Appointments->find($appointmentID);

    if (!is_object($Appointment)) {
        PerchUtil::redirect($API->app_path());
    }

    $details = $Appointment->to_array();
} else {
    PerchUtil::redirect($API->app_path());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newDate = trim((string) PerchUtil::post('appointmentDate'));
    $newSlot = trim((string) PerchUtil::post('slotLabel'));
    $confirmed = PerchUtil::post('appointmentConfirmed') ? 1 : 0;

    $date = DateTime::createFromFormat('Y-m-d', $newDate);

    if (!$date || $newSlot === '') {
        $message = $HTML->failure_message('Please provide a valid date and time.');
    } else {
        $update = [
            'appointmentDate' => $date->format('Y-m-d'),
            'appointmentDateLabel' => $date->format('D j M Y'),
            'slotLabel' => $newSlot,
            'appointmentConfirmed' => $confirmed,
        ];

        $wasConfirmed = isset($details['appointmentConfirmed']) && (int)$details['appointmentConfirmed'] === 1;

        if ($confirmed && !$wasConfirmed) {
            $update['confirmedAt'] = date('Y-m-d H:i:s');
        }

        if (!$confirmed) {
            $update['confirmedAt'] = null;
        }

        $Appointment->update($update);
        $Appointment = $Appointments->find($appointmentID);
        $details = $Appointment->to_array();

        $message = $HTML->success_message('Appointment updated successfully.');
    }
}
