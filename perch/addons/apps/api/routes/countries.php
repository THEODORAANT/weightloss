<?php
    //include(__DIR__ .'/../../../../core/inc/api.php');
include(__DIR__ .'/../../../../core/runtime/runtime.php');
require_once __DIR__ . '/../auth.php';
$countries=[];

$countries=get_countries();
 echo json_encode(["countries" => $countries]);


 ?>
