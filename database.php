<?php
$mysqli = new mysqli('localhost', 'capitalone', 'hello', 'world');
if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
}
?>
