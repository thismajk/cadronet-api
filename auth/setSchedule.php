<?php
require_once "../config.php";

$data = json_decode(file_get_contents("php://input"));

$id = SECURE($data->id);
$hourIn = SECURE($data->hourIn);
$hourOut = SECURE($data->hourOut);
$date = SECURE($data->date);

mysqli_query($conn, "INSERT INTO `schedule` (`id`, `userId`, `hourIn`, `hourOut`, `day`) VALUES (NULL, '".$id."', '".$hourIn."', '".$hourOut."', '".$date."')");

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
