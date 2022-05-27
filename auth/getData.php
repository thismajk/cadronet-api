<?php
require_once "../config.php";

$data = json_decode(file_get_contents("php://input"));

$fn = SECURE($data->functionName);
$userKey = SECURE($data->userKey);

if(auth($conn, $userKey) !== true){
    exit();
}

switch($fn){
    case "getUserName": 
        getUserName($conn, $userKey);
        break;
    case "getAvailability";
        getAvailability($conn, $userKey);
        break;
    case "getEmployeesAv":
        getEmployeesAv($conn, $userKey);
        break;
    case "getEmployeesSchedule":
        getEmployeesSchedule($conn, $userKey);
        break;
    case "getSchedule":
        getSchedule($conn, $userKey);
        break;
    case "getPin":
        getPin($conn, $userKey);
        break;
    case "getDashboard":
        getDashboard($conn, $userKey);
        break;
}

function getUserID($conn, $userKey){
    $query = mysqli_query($conn, 'SELECT userID FROM sessions WHERE userKey = "'.$userKey.'"');
    while($row = mysqli_fetch_array($query)){
        $userID = $row['userID'];
    }
    return $userID;
}


function getUserName($conn, $userKey){
    $userID = getUserID($conn, $userKey);

    $query = mysqli_query($conn, 'SELECT firstName, lastName FROM users WHERE id="'.$userID.'"');

    while($row = mysqli_fetch_array($query)){
        $firstName = $row['firstName'];
        $lastName = $row['lastName'];
    }

    $userName = $firstName." ".$lastName;
    echo '{"userName": "'.$userName.'"}';
}

function getAvailability($conn, $userKey){
    $userID = getUserID($conn, $userKey);

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
}
function getEmployeesAv($conn, $userKey){
    $queryUsers = mysqli_query($conn, 'SELECT id, firstName, lastName  FROM users');
    $numUsers = mysqli_num_rows($queryUsers);

    $result = '{ "users": "'.$numUsers.'",';
    $usersId = '"usersId": [';
    $countUsers = 0;
    while($rowUsers = mysqli_fetch_array($queryUsers)){
        $id = $rowUsers['id'];
        $firstName = $rowUsers['firstName'];
        $lastName = $rowUsers['lastName'];
        $countUsers++;

        $queryAv = mysqli_query($conn, 'SELECT * FROM availability WHERE userId = "'.$id.'"');
        $numAv = mysqli_num_rows($queryAv);
        $countAv = 0;
        
        $result = $result.'"'.$id.'": { "id": "'.$id.'",';
        $result = $result.'"fistName": "'.$firstName.'", "lastName": "'.$lastName.'", "availability":';
        if($numAv == 0){
            $result = $result.'"null"';
        }else{
            $result = $result.'{';
            while($rowAv = mysqli_fetch_array($queryAv)){
                $countAv++;
                $day = $rowAv['day'];
                $av = $rowAv['availability'];
                if($av == 1){
                    $av = "Dostępny";
                }
                else{
                    $av = "Niedostępny";
                }
                
                $result = $result.'"'.$day.'": "'.$av.'"'; 
        
                if($countAv < $numAv){
                    $result = $result.",";
                }
            }
            $result = $result.'}';
        }

        
        $result = $result."},";
        if($countUsers < $numUsers){
            
            $usersId = $usersId.'"'.$id.'", ';
        }
        else{
            $usersId = $usersId.'"'.$id.'"] ';
        }
    }

       

    $result = $result." ".$usersId."}";
    echo $result;
}   
function getEmployeesSchedule($conn, $userKey){

    $queryUsers = mysqli_query($conn, 'SELECT id FROM users');
    $numUsers = mysqli_num_rows($queryUsers);

    $result = '{';
    $countUsers = 0;
    while($rowUsers = mysqli_fetch_array($queryUsers)){
        $id = $rowUsers['id'];
        $countUsers++;

        $querySchedule = mysqli_query($conn, 'SELECT * FROM schedule WHERE userId = "'.$id.'"');
        $numSchedule = mysqli_num_rows($querySchedule);
        $countSchedule = 0;
        
        $result = $result.'"'.$id.'": { "id": "'.$id.'",';
        $result = $result.'"schedule":';
        if($numSchedule == 0){
            
            if($countUsers < $numUsers){
            
                $result = $result.'"null"},';
            }
            else{
                $result = $result.'"null"}';
            }
        }else{
            $result = $result.'{';
            while($rowSchedule = mysqli_fetch_array($querySchedule)){
                $countSchedule++;
                $day = $rowSchedule['day'];
                $hourIn = $rowSchedule['hourIn'];
                $hourOut = $rowSchedule['hourOut'];
                
                $result = $result.'"'.$day.'": {"hourIn": "'.$hourIn.'", "hourOut": "'.$hourOut.'"}'; 
        
                if($countSchedule < $numSchedule){
                    $result = $result.",";
                }
            }
            if($countUsers < $numUsers){
            
                $result = $result.'}},';
            }
            else{
                $result = $result.'}}';
            }
            
        }
    }
    $result = $result."}";
    echo $result;
}

function getSchedule($conn, $userKey){
    $userID = getUserID($conn, $userKey);
    $query = mysqli_query($conn, 'SELECT * FROM schedule WHERE userId = "'.$userID.'"');
    $numRows = mysqli_num_rows($query);
    $count = 0;
    $result = "{";
    while($row = mysqli_fetch_array($query)){
        $day = $row['day'];
        $hourIn = $row['hourIn'];
        $hourOut = $row['hourOut'];
        $count++;
        $result = $result.'"'.$day.'": {"hourIn": "'.$hourIn.'", "hourOut": "'.$hourOut.'" }';
        if($numRows > $count){
            $result = $result.",";
        }
    }
    $result = $result."}";
    echo $result;
}

function getPin($conn, $userKey){
    $userID = getUserID($conn, $userKey);
    $query = mysqli_query($conn, 'SELECT pin FROM usersPin WHERE userId = "'.$userID.'"');
    while($row = mysqli_fetch_array($query)){
        $pin = $row['pin'];
    }

    echo $pin;
}

function getDashboard($conn, $userKey){
    $userID = getUserID($conn, $userKey);
    $query = mysqli_query($conn, 'SELECT * FROM workRegisted WHERE userId = "'.$userID.'"');
    $workReg = mysqli_num_rows($query);

    $workHours = 0;
    while($row = mysqli_fetch_array($query)){
        $workStart = $row['workStart'];
        $workEnd = $row['workEnd'];
        if($workEnd == null){
            $workEnd = time();
        }
        $workHours = $workHours + (($workEnd - $workStart) / (60*60));
    }
    $workHours = round($workHours, 2);

    $query = mysqli_query($conn, 'SELECT * FROM schedule WHERE userId = "'.$userID.'"');
    $workPlan = mysqli_num_rows($query);

    $profit = $workHours * 20;


    $result = '{"workReg": "'.$workReg.'", "workPlan": "'.$workPlan.'", "workHours": "'.$workHours.'", "profit": "'.$profit.'"}';

    echo $result;

}