<?php
# return user's favorite question id's

header("Content-Type: application/json");
require('database.php'); # connect to database

$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true); // store data in associative array

if(isset($json_obj["username"])){ // if signed in, proceed
    $name = $json_obj["username"];
    $command = $mysqli->prepare("SELECT q_id FROM favorites WHERE username=?");
    if(!$command){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $command->bind_param('s', $name);
    $command->execute();
    $result = $command->get_result();

    while($row = $result->fetch_assoc()){
        $ret[] = $row;
    }   

    if(isset($ret)){
        echo json_encode(
            $ret
        );
    } else {
        echo json_encode(
            (object) null
        );
    }

    $command->close();
} else {
    echo json_encode(
        (object) null
    );
}

?>