<?php
require_once "../config.php";
$data = json_decode(file_get_contents("php://input"));

$email = SECURE($data->email);
$password = SECURE($data->password);

$validate = true;
$error = "";

if(strlen($email) == 0){
    $error = "Wpisz email";
    $validate = false;
    $result = '{"info": "error", "error": "'.$error.'", "isLogin": "false"}';   
    exit($result);
}

if(strlen($password) == 0){
    $error = "Wpisz hasło";
    $validate = false;
    $result = '{"info": "error", "error": "'.$error.'", "isLogin": "false"}';   
    exit($result);
}

$query = mysqli_query($conn, 'SELECT * FROM users WHERE email="'.$email.'"');

if(mysqli_num_rows($query) == 0){
    $error = "Nieporwany email";
    $validate = false;
    $result = '{"info": "error", "error": "'.$error.'", "isLogin": "false"}';   
    exit($result);
}

while($row = mysqli_fetch_array($query)){
    $password_hash = $row['password'];
    $userId = $row['id'];
}

if(!password_verify($password, $password_hash)){
    $error = "Nieporwane hasło";
    $validate = false;
    $result = '{"info": "error", "error": "'.$error.'", "isLogin": "false"}';   
    exit($result);
}

$userKey = md5(uniqid(rand(), true));
$loginDate = date("Y-m-d H:i:s");

mysqli_query($conn, "INSERT INTO `sessions` (`id`, `userID`, `userKey`, `loginDate`) VALUES (NULL, '".$userId."', '".$userKey."', '".$loginDate."')");


echo '{"info": "success", "isLogin": "true", "userKey": "'.$userKey.'"}';
