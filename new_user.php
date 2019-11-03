<!DOCTYPE html>
<html lang="en">
<head>
    	<title>Create Account</title>
    	<meta charset="utf-8"/>
	<link rel="stylesheet" href="theme.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
</head>
<body>
    <header>
        	<!--<h1>CREATE NEW USER</h1>-->
   </header>
	<div class="container4">
   		<form method="POST">
        	<p>
            		 <input placeholder="New username" type="text" name="username"  id="username" required/>
		</p>
		<p>
            		<input placeholder="New password" type="text" name="password" id="password" required />
        	</p>
        	<p>
           		<input type = "submit" value = "Create" id="create">
        	</p>
   		</form>
		<form action="login.html">
			<input type = "submit" value = "Cancel" id="back">
		</form>
	</div>
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
<script>
	$("#back, #create").button();
</script>
</html>
