<?php
require_once "../config.php";

$data = json_decode(file_get_contents("php://input"));
$userKey = SECURE($data->userKey);

echo $userKey;  
mysqli_query($conn, 'DELETE FROM `sessions` WHERE `userKey` ="'.$userKey.'"');