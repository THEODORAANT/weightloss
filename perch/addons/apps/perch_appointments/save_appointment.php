<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/../../../runtime.php';

header('Content-Type: application/json');

$API = new PerchAPI(1.0, 'perch_appointments');
$DB = $API->get('DB');
$table = PERCH_DB_PREFIX . 'appointments';

// Ensure the table exists for storing appointment submissions.
$DB->execute('CREATE TABLE IF NOT EXISTS `'.$table.'` (
  `appointmentID` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `memberID` INT UNSIGNED NULL DEFAULT NULL,
  `productSlug` VARCHAR(255) NOT NULL,
  `productName` VARCHAR(255) NOT NULL,
  `productPrice` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `appointmentDate` DATE NOT NULL,
  `appointmentDateLabel` VARCHAR(255) NOT NULL,
  `slotLabel` VARCHAR(255) NOT NULL,
  `goal` TEXT NOT NULL,
  `medical` TEXT NOT NULL,
  `notes` TEXT NULL,
  `createdAt` DATETIME NOT NULL,
  PRIMARY KEY (`appointmentID`),
  INDEX `member_lookup` (`memberID`),
  INDEX `product_lookup` (`productSlug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = $_POST;
}

$required = ['product_slug', 'product_name', 'appointment_date', 'appointment_date_label', 'slot', 'goal', 'medical'];
foreach ($required as $key) {
    if (!isset($input[$key]) || trim((string) $input[$key]) === '') {
        http_response_code(400);
        echo json_encode([
            'result' => false,
            'message' => 'Missing required appointment details.',
        ]);
        exit;
    }
}

$date = DateTime::createFromFormat('Y-m-d', $input['appointment_date']);
if (!$date) {
    http_response_code(400);
    echo json_encode([
        'result' => false,
        'message' => 'Invalid appointment date.',
    ]);
    exit;
}

$memberID = null;
if (function_exists('perch_member_logged_in') && perch_member_logged_in()) {
    $memberID = perch_member_get('id');
}

$data = [
    'memberID' => $memberID ? (int) $memberID : null,
    'productSlug' => trim((string) $input['product_slug']),
    'productName' => trim((string) $input['product_name']),
    'productPrice' => isset($input['product_price']) && is_numeric($input['product_price']) ? (float) $input['product_price'] : 0,
    'appointmentDate' => $date->format('Y-m-d'),
    'appointmentDateLabel' => trim((string) $input['appointment_date_label']),
    'slotLabel' => trim((string) $input['slot']),
    'goal' => trim((string) $input['goal']),
    'medical' => trim((string) $input['medical']),
    'notes' => isset($input['notes']) ? trim((string) $input['notes']) : '',
    'createdAt' => date('Y-m-d H:i:s'),
];

$inserted = $DB->insert($table, $data);

if (!$inserted) {
    http_response_code(500);
    echo json_encode([
        'result' => false,
        'message' => 'Unable to save appointment details.',
    ]);
    exit;
}

echo json_encode(['result' => true]);
