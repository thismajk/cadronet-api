<?php
require_once "../config.php";

$data = json_decode(file_get_contents("php://input"));

$date = SECURE($data->date);
$userKey = SECURE($data->userKey);
$title = SECURE($data->title);

if($title == "Dostępny"){
    $av = 1;
}
else if($title == "Niedostępny"){
    $av = 0;
}
else{
    echo '{"info": fail}';
}

$query = mysqli_query($conn, 'SELECT userID FROM sessions WHERE userKey = "'.$userKey.'"');
while($row = mysqli_fetch_array($query)){
    $userID = $row['userID'];
}

mysqli_query($conn, "INSERT INTO `availability` (`id`, `userId`, `comment`, `hourIn`, `hourOut`, `day`, `availability`) VALUES (NULL, '".$userID."', 'null', '00:00', '00:00', '".$date."', '".$av."')");
$query = mysqli_query($conn, 'SELECT * FROM `availability` WHERE `userId` = "'.$userID.'"');

    $result = "{";
    $count = 0;
    while($row = mysqli_fetch_array($query)){   
        if($count > 0){
            $result = $result.", ";
        }
        $count++;
        $availability = $row['availability']; 
        $day = $row['day'];

        if($availability == 1){
            $availability = "Dostępny";
        }
        else{
            $availability = "Niedostępny";
        }
        $result = $result.'"'.$day.'": { "status": "'.$availability.'" } ';
    }

    $result = $result . "}";
    echo $result;