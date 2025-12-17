<?php
include __DIR__ . '/scripts/send_christmas_delivery_schedule.php';

	PerchScheduledTasks::register_task('perch_members', 'send_christmas_delivery_schedule', 1, function($last_run){

		$API  = new PerchAPI(1.0, 'perch_members');
		$result = perch_members_send_christmas_delivery_schedule($last_run);
		echo "result";print_r($result);
		return $result;
});
/*
include __DIR__ . '/scripts/send_christmas_delivery_schedule.php';

PerchScheduledTasks::register_task(
    'perch_members',
    'christmas_delivery_schedule',
    1440,
    'perch_members_send_christmas_delivery_schedule'
);
*/
