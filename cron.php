<?php
// Config
$apiKey = 'YOUR_EMAILOCTOPUS_API_KEY';
$listId = 'YOUR_LIST_ID';
$today = new DateTime();

// Connect to your database
$pdo = new PDO('mysql:host=localhost;dbname=your_db', 'your_user', 'your_password');

// Fetch users whose order was exactly 8 days ago
$query = "
    SELECT email, first_order_date
    FROM users
    WHERE DATEDIFF(CURDATE(), first_order_date) = 8
";
$stmt = $pdo->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Loop through users and send reminders
foreach ($users as $user) {
    $email = $user['email'];

    $data = [
        'email_address' => $email,
        'api_key' => $apiKey,
        'tags' => ['8-day-reminder'],
        'fields' => ['REMINDER' => 'true'],
        'status' => 'subscribed'
    ];

    $ch = curl_init("https://emailoctopus.com/api/1.6/lists/{$listId}/contacts");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 || $httpCode === 201) {
        echo "Reminder sent to $email\n";
    } else {
        echo "Failed to send to $email: $response\n";
    }
}
?>
