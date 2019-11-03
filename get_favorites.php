<?php
header("Content-Type: application/json");
require('database.php'); # connect to database

$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);

session_start();

if(isset($_SESSION["username"])){ // if signed in, proceed
    $name = $_SESSION["username"];
    $command = $mysqli->prepare("SELECT * FROM favorites where username = ?");
    if(!$command){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $command->bind_param('s', $name);
    $command->execute();
    $result = $command->get_result();

    while($row = $result->fetch_assoc()){
        $content[] = $row;
    }

    if(isset($content)){
        echo json_encode(
            $content
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