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
	# not logged in
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
	// global variables
	//
	let catID = {}; // dictionary holding category-id pairs

	//
	// event listeners
	//
	document.getElementById("search").addEventListener("click", function(event) {displayEvents(0);}, false);
	document.getElementById("dateMinParam").addEventListener("change", dateCheck, false);
	document.getElementById("dateMaxParam").addEventListener("change", dateCheck, false);
	document.getElementById("catParam").addEventListener("input", fetchCategories, false);

	//
	// functions (extra functions maybe included as files)
	//
	function displayEvents(event, offset){
		let body = document.getElementById("body");
		
		let results = document.createElement("TABLE");
		search(offset).then(content => {
			console.log(content);
			let arr = JSON.stringify(content); 
			for (var i = 0; i < arr.length; i++){
    var obj = arr[i];
    for (var key in obj){
        var attrName = key;
        var attrValue = obj[key];
	console.log(attrName);
    }
}
                	/*for (let num in data){
                       	 	if(data.hasOwnProperty(num)){ // good practice
                                	let question = data[num];
                                	console.log(question);
                        	}
                	}*/
			body.appendChild(results);
		})
	
		
	}
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
	function search(offset){ // search button pressed
		const val = document.getElementById("valueParam").value;
		const datMin = document.getElementById("dateMinParam").value;
		const datMax = document.getElementById("dateMaxParam").value;
		const category = document.getElementById("catParam").value;
		
		let url = "http://jservice.io/api/clues?"
		.concat("value="+encodeURIComponent(val))
		.concat("&min_date="+encodeURIComponent(datMin))
		.concat("&max_date="+encodeURIComponent(datMax));
		if(category in catID){
			url = url.concat("&category="+encodeURIComponent(catID[category]));
		} else {
			url = url.concat("&category=0"); // returns nothing
		}
		if(offset != null){
			url = url.concat("&offset="+encodeURIComponent(offset));
		}
		
		// call api
		return fetch(url)
		.then(response => response.json())
		.then(content => {
                        console.log(content);
                     	return content;
                })
               	.catch((err) => {
                      	console.log(err);
            	})

		//return null;					
	}
	function fetchCategories(event){ // call jService search API
		let dl = document.getElementById("category");
		while(dl.firstChild){
			dl.removeChild(dl.firstChild); // clear the datalist in preparation for new elements
		}
		catID = {};

		// call search api
		fetch("http://jservice.io/search?query="+document.getElementById("catParam").value)
		.then(response => response.text())
		.then(response => {
			
			// web scraping in pure javascript!
			let html = document.createElement("HTML");
			html.innerHTML = response;
			let table = html.getElementsByTagName("tbody")[0];
			let rows = table.children;
			for(let i = 0; i < rows.length; i++){
				let txt = rows[i].children[0].children[0].textContent;
				let id = rows[i].children[0].children[0].getAttribute("href").substring(9);	
			
				catID[txt] = id; // map category name to id	
	
				let item = document.createElement("OPTION");
				item.value = rows[i].children[0].children[0].textContent;
				dl.appendChild(item);
			}
		});
	}
	
	
</script>
</html>
