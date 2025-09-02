<?php //ob_start(); session_start() ;
if(isset($_GET["ref"])){
$_SESSION["referrer"]=$_GET["ref"];
}

?>
