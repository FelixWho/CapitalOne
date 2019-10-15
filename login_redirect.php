<?php
if(isset($_POST['username'])){
    	$name = trim((string)$_POST['username']);
	
    	require('database.php'); # connect to database
    	$command = $mysqli->prepare("SELECT COUNT(*), id, password FROM users WHERE username=?");
	
	# fetch password hash
    	$command->bind_param('s', $name);
    	$command->execute();
    	$command->bind_result($cnt, $user_id, $pwd_hash);
    	$command->fetch();
    	$pwd_entry = $_POST['password'];
	
    	// Compare the submitted password to the actual password hash
    	if($cnt == 1 && password_verify($pwd_entry, $pwd_hash)){
        	session_start();
        	$_SESSION['user_id'] = $user_id;
		$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32)); // CSRF token
		header("Location: story_browser.php"); // redirect: login success
    	} else{
		header("Location: login.html"); // redirect: password failed
    	}
exit;
?>
