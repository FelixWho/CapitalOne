<?php
# return user's favorite question id's

header("Content-Type: application/json");
require('database.php'); # connect to database

$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true); // store data in associative array

session_start();

if(isset($_SESSION["username"])){ // if signed in, proceed
    $name = $_SESSION["username"];
    $id = $json_obj["id"];

    $command = $mysqli->prepare("DELETE FROM favorites WHERE q_id = ? AND username = ?");
    if(!$command){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $command->bind_param('ss', $id, $name);
    $command->execute();
    $command->close();

    echo json_encode(array(
        "success" => true
    ));

} else {
    echo json_encode(array(
        "success" => false
    ));
}

?>