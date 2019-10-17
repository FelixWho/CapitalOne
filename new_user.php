<!DOCTYPE html>
<html lang="en">
<head>
		<link rel = "stylesheet" type = "text/css" href = "theme.css" />
    	<title>Create Account</title>
    	<meta charset="utf-8"/>
</head>
<body>
    <header>
        	<h1>CREATE NEW USER</h1>
   </header>
   		<form method="POST">
        	<p>
            		<label>New Username: </label> <input type="text" name="username"  id="username" required/>
		</p>
		<p>
            		<label>Set Password: </label> <input type="text" name="password" id="password" required />
        	</p>
        	<p>
           		<input type = "submit" value = "Create">
        	</p>
   	</form>
<?php
if(isset($_POST['username']) && isset($_POST['password'])){
	require('database.php'); # connect to database
	
	$username = trim((string)$_POST['username']);
	$password = (string)$_POST['password'];
	
	$command = $mysqli->prepare("insert into users (username, password) values (?, ?)");
	if(!$command){
		printf("MySQL Prepare Failed With Error: %s\n", $mysqli->error);
		exit;
	}
	$command->bind_param('ss', $username, password_hash($password, PASSWORD_DEFAULT));
	$command->execute();
	$command->close();
	header('Location: login.html');	
}
?>
</body>
</html>
