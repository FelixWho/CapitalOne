<?php
# return user's favorite question id's

header("Content-Type: application/json");
require('database.php'); # connect to database

$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true); // store data in associative array

if(isset($json_obj["username"])){ // if signed in, proceed
    $name = $json_obj["username"];
    $id = $json_obj["id"];
    $question = $json_obj["question"];
    $answer = $json_obj["answer"];
    $category = $json_obj["category"];
    $date = $json_obj["date_aired"];
    $command = $mysqli->prepare("INSERT INTO favorites (username, q_id, question, answer, category, date_aired) VALUES (?,?,?,?,?,?)");
    if(!$command){
        printf("Query Prep Failed: %s\n", $mysqli->error);
        exit;
    }

    $command->bind_param('ssssss', $name, $id, $question, $answer, $category, $date);
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