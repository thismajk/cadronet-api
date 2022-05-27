<?php
header("Access-Control-Allow-Headers: *");

define("DB_HOST", "localhost:8889");
define("DB_USER", "root");
define("DB_PASSWORD", "root");
define("DB_NAME", "cadronet");

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

define("PASSWORD_HASH", PASSWORD_DEFAULT);

if(!isset($_SESSION)) {
    session_start();
 }

function SECURE($i){
    return htmlentities($i);
}

function auth($conn, $userKey){
    $query = mysqli_query($conn, 'SELECT * FROM `sessions` WHERE `userKey` = "'.$userKey.'"');

    if(mysqli_num_rows($query) == 0){
        return false;
    }
    else{
        return true;
    }
}

function emailValidate($i){
    if(filter_var($i, FILTER_VALIDATE_EMAIL)){
        return true;
    }
    else{
        return false;
    }
}

