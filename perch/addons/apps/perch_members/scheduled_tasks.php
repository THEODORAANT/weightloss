<?php

include __DIR__ . '/scripts/send_christmas_delivery_schedule.php';

PerchScheduledTasks::register_task(
    'perch_members',
    'christmas_delivery_schedule',
    1440,
    'perch_members_send_christmas_delivery_schedule'
);
