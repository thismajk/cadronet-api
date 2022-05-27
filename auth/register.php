<?php
require_once "../config.php";

$data = json_decode(file_get_contents("php://input"));

$email = SECURE($data->email);
$firstName = SECURE($data->firstName);
$lastName = SECURE($data->lastName);
$password = SECURE($data->password);
$repPassword = SECURE($data->repPassword);
$accept = SECURE($data->accept);

$validate = true;
$error = "";

if(emailValidate($email) !== true){
    $validate = false;
    $error = "Wpisz poprawny email";
    $result = '{"info": "error", "error": "'.$error.'"}';   
    exit($result);
}

$query = mysqli_query($conn, 'SELECT * FROM users WHERE email = "'.$email.'"');
$query = mysqli_num_rows($query);

if($query != 0){
    $error = "odany email jest juz zajęty";
    $validate = false;
    $result = '{"info": "error", "error": "'.$error.'"}';   
    exit($result);
}

if(strlen($firstName) < 2){
    $validate = false;
    $error = "Wpisz poprawne imię";
    $result = '{"info": "error", "error": "'.$error.'"}';   
    exit($result);
}
if(strlen($lastName) < 2){
    $validate = false;
    $error = "Wpisz poprawne nazwisko";
    $result = '{"info": "error", "error": "'.$error.'"}';   
    exit($result);
}
if(strlen($password) < 5){
    $validate = false;
    $error = "Hasło jest za krótkie";
    $result = '{"info": "error", "error": "'.$error.'"}';   
    exit($result);
}
else if ($password !== $repPassword) {
    $error = "Hasła różnią się";
    $validate = false;
    $result = '{"info": "error", "error": "'.$error.'"}';   
    exit($result);
}

if ($accept != true) {
    $error = "Nie zaakceptowano polityk";
    $validate = false;
    $result = '{"info": "error", "error": "'.$error.'"}';   
    exit($result);
}

$password = password_hash($password, PASSWORD_HASH);

mysqli_query($conn, "INSERT INTO `users` (`id`, `email`, `firstName`, `lastName`, `password`) VALUES (NULL, '$email', '$firstName', '$lastName', '$password')");
$result = '{"info": "success"}';
echo $result;


