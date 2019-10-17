<?php
$mysqli = new mysqli('localhost', 'summit', 'capitalone', 'capitalone');
if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
}
?>
