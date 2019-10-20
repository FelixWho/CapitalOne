<!DOCTYPE html>
<html lang="en">
<head>
        <title>Jeopardy Search Engine</title>
        <meta charset="utf-8"/>
	<!--include jQuery-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<body id = "body">
<?php
session_start();
if(isset($_SESSION['username'])){
	# logged in
	echo "<div><form action = 'logout.php'>";
   	echo "<input type='submit' value='Log Out' name='Logout'/>";
        echo "</form></div>";
} else {
	echo "<div><form action = 'login.html'>";
        echo "<input type='submit' value='Log In' name='Login'/>";
        echo "</form></div>";
}
?>

<!--search by date-->
<form action="result.html" method="GET">
<div>
<label for="dateParam">Date:</label>
<input type="date" id = "dateParam">
</div>

<!--search by difficulty value-->
<div>
<label for="valueParam">Difficulty:</label>
<input list="values" id = "valueParam">
<datalist id="values">
 	<option value="100">
  	<option value="200">
  	<option value="300">
  	<option value="400">
	<option value="500">
	<option value="600">
	<option value="800">
	<option value="1000">
</datalist>
</div>
<input type="submit" id = "search" value="Search">
<input type="submit" id = "random" value="I'm Feeling Adventurous"> <!--get random question like Google does-->
</form>

</body>
<script>
	//
	// event listeners
	//
	document.getElementById("search").addEventListener("click", search, false);
	//document.addEventListener("DOMContentLoaded", fetchCategories, false);
	document.getElementById("random").addEventListener("click", rand, false);	

	//
	// functions (extra functions maybe included as files)
	//
	function rand(event){ // redirect to random question page
		window.location("random.html");
	}
	function search(event){ // search button pressed
		const val = document.getElementById("valueParam").value;
		const dat = new Date(document.getElementById("dateParam").value).toISOString();
			
		let url = "http://jservice.io/api/clues?"
		.concat("value="+val)
		.concat("&min_date="+dat);
		
		// call api
		fetch(url)
		.then(response => response.json())
		.then(content => {
			console.log(content);
		});
					
	}
	function fetchCategories(event){ // call jService API
		fetch("http://jservice.io/api/category?id=1")
		.then(response => {
			let data = JSON.parse(JSON.stringify(response))
			console.log(data.title);
		})
		.then(data => console.log(data));
		//const xmlHttp = new XMLHttpRequest();
		
                //xmlHttp.open("GET", "http://jservice.io/api/category?id=1", true);
                //xmlHttp.addEventListener("load", callbackCategories, false);
                //xmlHttp.send(null);
	}
	function callbackCategories(event){
		data = JSON.parse(event.target.responseText);
		if(data.hasOwnProperty("status")){
			
			return;
		}
		console.log(data.title);
	}
	
	
</script>
</html>
