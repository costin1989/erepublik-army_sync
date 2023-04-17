<?php
header("Content-Type: application/json");
$response = array("message" => "Hello, World!");
echo json_encode($response);
?>
