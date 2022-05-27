<?php
require_once "../config.php";

$data = json_decode(file_get_contents("php://input"));
$pin = SECURE($data->pin);

$query = mysqli_query($conn, 'SELECT * FROM usersPin WHERE pin = "'.$pin.'"');
$num = mysqli_num_rows($query);


if($num>0){
    while($row = mysqli_fetch_array($query)){
        $userId = $row['userId'];
    }
    
    $query = mysqli_query($conn, 'SELECT firstName, lastName FROM users WHERE id = "'.$userId.'"');
    while($row = mysqli_fetch_array($query)){
        $firstName = $row['firstName'];
        $lastName = $row['lastName'];
    }
    $today = date('Y-m-d');

    $query = mysqli_query($conn, 'SELECT * FROM `workRegisted` WHERE `userId` = "'.$userId.'" AND `workEnd` IS NULL');
    $num = mysqli_num_rows($query);

    if($num == 0){
        mysqli_query($conn, 'INSERT INTO `workRegisted` (`id`, `userId`, `workStart`, `workEnd`, `date`) VALUES (NULL, "'.$userId.'", "'.time().'", NULL, "'.$today.'")');
        echo "zmiana rozpoczęta dla: ".$firstName." ".$lastName;
    }else{
        mysqli_query($conn, 'UPDATE `workRegisted` SET `workEnd` = "'.time().'" WHERE `userId` = "'.$userId.'" AND `workEnd` IS NULL');

        $query = mysqli_query($conn, 'SELECT * FROM `workRegisted` WHERE `userId` = "'.$userId.'" ORDER BY `workRegisted`.`id` DESC LIMIT 1;');
        while($row =  mysqli_fetch_array($query)){
            $workStart = $row['workStart'];
            $workEnd = $row['workEnd'];
        }
        $workedTime = $workEnd - $workStart;
        $workedTime = $workedTime / (60 * 60);
        echo "zmiana zakończona dla: ".$firstName." ".$lastName." Przepracowano: ".$workedTime."h";
        
    }
}else{
    echo 'niepoprawny pin';
}