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
<div>
<label for="dateMinParam">Minimum Date Aired:</label>
<input type="date" id = "dateMinParam">
</div>
<div>
<label for="dateMaxParam">Maximum Date Aired:</label>
<input type="date" id = "dateMaxParam">
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

<!--search by category-->
<div>
<label for="catParam">Category:</label>
<input list="category" id = "catParam">
<datalist id="category">
	<!--will populate with javascript + webscrape-->
</datalist>
</div>

<input type="submit" id = "search" value="Search">

</body>
<script>
	//
	// event listeners
	//
	document.getElementById("search").addEventListener("click", search, false);
	document.getElementById("dateMinParam").addEventListener("change", dateCheck, false);
	document.getElementById("dateMaxParam").addEventListener("change", dateCheck, false);
	document.getElementById("catParam").addEventListener("change", fetchCategories, false);

	//
	// functions (extra functions maybe included as files)
	//
	function dateCheck(event){ // prevents min date parameter from being greater than max date parameter and vice versa
		let dMin = new Date(document.getElementById("dateMinParam").value);
		let dMax = new Date(document.getElementById("dateMaxParam").value);
		if(event.target == document.getElementById("dateMinParam") && dMin > dMax) {
			document.getElementById("dateMaxParam").value = document.getElementById("dateMinParam").value;
		}
		if(dMax < dMin){
			document.getElementById("dateMinParam").value = document.getElementById("dateMaxParam").value;
		}
	}
	function search(event){ // search button pressed
		const val = document.getElementById("valueParam").value;
		const datMin = document.getElementById("dateMinParam").value;
		const datMax = document.getElementById("dateMaxParam").value;
		console.log(encodeURIComponent(datMin));	
		let url = "http://jservice.io/api/clues?"
		.concat("value="+encodeURIComponent(val))
		.concat("&min_date="+encodeURIComponent(datMin))
		.concat("&max_date="+encodeURIComponent(datMax));
		
		// call api
		fetch(url)
		.then(response => response.json())
		.then(content => {
			console.log(content);
		});
					
	}
	function fetchCategories(event){ // call jService search API
		fetch("http://jservice.io/search?query=a")
		.then(response => response.text())
		.then(response => {console.log(response);});
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
