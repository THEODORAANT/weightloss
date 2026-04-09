<?php

if (!is_object($Appointment)) {
    echo $HTML->failure_message('Appointment not found.');
    return;
}

echo $HTML->title_panel([
    'heading' => $Lang->get('Edit appointment #%s', $details['appointmentID']),
], $CurrentUser);

if ($message) echo $message;

echo '<div class="inner">';
echo '<form method="post">';

echo '<div class="field-wrap">';
echo '<label>Date</label>';
echo '<input type="date" name="appointmentDate" value="'.PerchUtil::html($details['appointmentDate']).'" required>';
echo '</div>';

echo '<div class="field-wrap">';
echo '<label>Time</label>';
echo '<input type="text" name="slotLabel" value="'.PerchUtil::html($details['slotLabel']).'" required>';
echo '</div>';

echo '<div class="field-wrap">';
echo '<label><input type="checkbox" name="appointmentConfirmed" value="1" '.(((int)$details['appointmentConfirmed'] === 1) ? 'checked' : '').'> Confirm appointment</label>';
echo '</div>';

echo '<div class="submit-bar">';
echo '<button type="submit" class="button button-icon icon-left">Save changes</button>';
echo '</div>';

echo '</form>';

echo '<hr>';
echo '<h2>Saved appointment details</h2>';
echo '<table class="d">';
foreach ($details as $key => $value) {
    echo '<tr>';
    echo '<th>'.PerchUtil::html($key).'</th>';
    echo '<td>'.PerchUtil::html((string)$value).'</td>';
    echo '</tr>';
}
echo '</table>';
echo '</div>';
