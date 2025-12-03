<?php


    $API   = new PerchAPI(1.0, 'perch_appointments');
    $Lang  = $API->get('Lang');

    $Paging = $API->get('Paging');
    $Paging->set_per_page(10);

    $Appointment = new PerchAppointments_Appointments($API);
    $appointments = $Appointment->all($Paging);

?>
<div class="widget">
	<h2>
		<?php echo $Lang->get('Appointments'); ?>
		<a href="<?php echo PerchUtil::html(PERCH_LOGINPATH.'/addons/apps/perch_appointments/edit/'); ?>" class="add button"><?php echo $Lang->get('Add Announcement'); ?></a>
	</h2>
	<div class="bd">
		<?php
			if (PerchUtil::count($appointments)) {
				echo '<ul>';
				foreach($appointments as $Appointment) {
					echo '<li>';
						echo '<a href="'.PerchUtil::html(PERCH_LOGINPATH.'/addons/apps/perch_appointments/edit/?id='.$Appointment->id()).'">';
							echo PerchUtil::html($Appointment->slotLabel());
							echo '<span class="note">'.PerchUtil::html(date('d M Y, h:i s', strtotime($Appointment->appointmentDate()))).'</span>';
						echo '</a>';
					echo '</li>';
				}
				echo '</ul>';
			}
		?>
	</div>
</div>
