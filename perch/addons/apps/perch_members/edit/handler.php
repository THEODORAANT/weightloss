<?php
    # include the API
    include('../../../../core/inc/api.php');

    $API  = new PerchAPI(1.0, 'perch_members');
// Get raw POST data
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

if ($data && isset($data['selectId']) && isset($data['selectedValue'])) {
    $id = htmlspecialchars($data['selectId']);
    $value = htmlspecialchars($data['selectedValue']);
        $Documents = new PerchMembers_Documents($API);
$target_dir ="/perch/addons/apps/perch_members/documents/";
/*
$filePath = $target_dir.'/'.$Document->documentName();

if (file_exists($filePath)) {
    if (unlink($filePath)) {
        echo "File deleted successfully.";
    } else {
        echo "Error deleting the file.";
    }
} else {
    echo "File does not exist.";
}*/
   $r= $Documents->update_document_status($data['selectId'],$data['selectedValue']);
    echo "Received from {$id}: {$value}:{$r}";
} else {
    echo "Invalid input.";
}
?>
