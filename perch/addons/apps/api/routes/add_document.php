<?php
    include(__DIR__ .'/../../../../core/runtime/runtime.php');

    require_once __DIR__ . '/../auth.php';
header('Content-Type: application/json');

    $token = get_bearer_token();
    $payload = verify_token($token);
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$payload) {
        http_response_code(401);
        echo json_encode(["error" => "Unauthorized"]);
        exit;
    }

// 2. Check if file and documentType are provided
if (!isset($_FILES['image']) && !isset($_FILES['video'])) {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded']);
    exit;
}

if (!isset($_POST['documentType'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing documentType']);
    exit;
}

// 3. Prepare fake SubmittedForm object to call your existing function
class SimpleForm
{
    public $files;
    public $data;

    public function __construct($files, $data)
    {
        $this->files = $files;
        $this->data = $data;
    }
}
$memberID = $payload['user_id'];
//echo "memberID".$memberID;
$form = new SimpleForm($_FILES, ['documentType' => $_POST['documentType']]);
perch_member_upload_document_api($memberID,$form);
echo json_encode(['success' => true]);

    ?>
