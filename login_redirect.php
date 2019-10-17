<?php
if(isset($_POST['username'])){
    	$name = (string)$_POST['username'];
    	require('database.php'); # connect to database
    	$command = $mysqli->prepare("SELECT COUNT(*), username, password FROM users WHERE username=?");
	
	# fetch password hash
    	$command->bind_param('s', $name);
    	$command->execute();
    	$command->bind_result($cnt, $username, $pwd_hash);
    	$command->fetch();
    	$pwd_entry = $_POST['password'];
	
    	// Compare the submitted password to the actual password hash + salt
    	if($cnt == 1 && password_verify($pwd_entry, $pwd_hash)){
        	session_start();
        	$_SESSION['username'] = $username;
		$_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32)); // CSRF token, best security practices
		header("Location: search.html"); // redirect: login success
		exit;
    	} else{
		header("Location: login.html"); // redirect: login failed
		exit;
    	}
}
header("Location: login.html"); // redirect: login failed: no set username
exit;
?>
