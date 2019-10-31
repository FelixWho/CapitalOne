<!DOCTYPE html>
<html lang="en">
<head>
    <title>Jeopardy Search Engine</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!--include jQuery-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
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
<input type="text" id = "dateMinParam" placeholder="Pick minimum date" readonly>
</div>
<div>
<input type="text" id = "dateMaxParam" placeholder="Pick maximum date" readonly>
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
	<!--will populate with javascript + webscraping-->
</datalist>
</div>

<input type="submit" id = "search" value="Search">

<div id="results">
	<!--will populate with search results-->
</div>

</body>
<script>

    /******
                jQuery UI elements
				intended for usage across different web browsers
    ******/

    $("#dateMinParam").datepicker({
        yearRange: "1950:+0",
        changeMonth: true,
        changeYear: true,
        onSelect: function() {
            dateCheck(this);
        }
    });

    $("#dateMaxParam").datepicker({
        yearRange: "1950:+0",
        changeMonth: true,
        changeYear: true,
        onSelect: function() {
            dateCheck(this);
        }
    });

    $("#results").accordion({ header: "h3", collapsible: true, active: false });

	/******
                GLOBAL VARIABLES
    ******/

	let catID = {}; // dictionary holding category-id pairs
	let results = {};

	/******
                EVENT LISTENERS
    ******/

	document.getElementById("search").addEventListener("click", function(event) {displayEvents(0);}, false);
	document.getElementById("dateMinParam").addEventListener("change", dateCheck, false);
	document.getElementById("dateMaxParam").addEventListener("change", dateCheck, false);
	document.getElementById("catParam").addEventListener("input", fetchCategories, false);

	/******
                FUNCTIONS
                (extra functions may be included as files) 
    ******/

	function displayEvents(event, offset){
		if(!checkCategoryBox()){
			return;
		}

        // clear table UI
        let tab = document.getElementById("results");
        if(tab) { tab.innerHTML=""; }

		search(offset).then(content => {

		let arr = JSON.parse(JSON.stringify(content)); 
		results = arr;
		for (let key in arr){
			if(arr.hasOwnProperty(key)){
				let question = arr[key];

				let que = document.createElement("H3");
				let ans = document.createElement("DIV");

				que.id = question.id;
				que.innerText = question.question;
				ans.id = question.id;
				ans.innerHTML = "<p>"+question.answer+"</p>"
								+ "<p>"+question.airdate+"</p>"
								+ "<p>Value: "+question.value+"</p>"
								+ "<p>Category: "+question.category.title+"</p>";


				tab.appendChild(que);
				tab.appendChild(ans);
			}
		}
		$("#results").accordion("refresh");
		});
	}

	function checkCategoryBox(){ // helper function that checks if category box has input,
								 // and if the input is a valid category
		let box = document.getElementById("catParam");
		if(box.value.length > 0 && Object.keys(catID).indexOf(box.value) == -1){
			// user inputted a category that doesn't exist, alert
			alert("Please input a valid category from the drop-down menu.");
			return false;
		}
		return true;
	}

	function search(offset){ // search button pressed
		const val = document.getElementById("valueParam").value;
		const datMin = document.getElementById("dateMinParam").value.replace("/", "-");
		const datMax = document.getElementById("dateMaxParam").value.replace("/", "-");
		const category = document.getElementById("catParam").value;
		
		let callURLs = [];
		let url = "http://jservice.io/api/clues?"
		.concat("value="+encodeURIComponent(val))
		.concat("&min_date="+encodeURIComponent(datMin))
		.concat("&max_date="+encodeURIComponent(datMax));
		if(offset != null){
			url = url.concat("&offset="+encodeURIComponent(offset));
        }
		if(category in catID){
			url = url.concat("&category="+encodeURIComponent(catID[category]));
		} else if(category.length > 0){
			url = url.concat("&category=0"); // returns nothing
		}

		// call jService clues api
		return fetch(url)
		.then(response => response.json())
		.then(content => {
			return content;
		})
		.catch((err) => {
			console.log(err);
		})		
	}

	function fetchCategories(event){ // call jService search API
		// call search api
		//console.log("fetching categories with: "+ "http://jservice.io/search?query="+event.target.value.replace(/ /g,"+"));
		fetch("http://jservice.io/search?query="+event.target.value.replace(/ /g,"+"))
		.then(response => response.text())
		.then(response => {
			let dl = document.getElementById("category");
			while(dl.firstChild){
				dl.removeChild(dl.firstChild); // clear the datalist in preparation for new elements
			}
			catID = {};
			
			// web scraping in pure javascript using small trick :D
			let html = document.createElement("HTML");
			html.innerHTML = response; // place web html data into DOM object
			let table = html.getElementsByTagName("tbody")[0];
			let rows = table.children;
			for(let i = 0; i < rows.length; i++){ // traverse DOM
				let txt = rows[i].children[0].children[0].textContent;
				let id = rows[i].children[0].children[0].getAttribute("href").substring(9);	
			
				catID[txt] = id; // map category name to id	
	
				let item = document.createElement("OPTION");
				item.value = rows[i].children[0].children[0].textContent;
				dl.appendChild(item);
			}
		})
		.catch((err) => {
			console.log(err); // good practice
		});
	}

	function dateCheck(picker){ // ensures min date <= max date
        let dMin = new Date(document.getElementById("dateMinParam").value.replace("/", "-"));
        let dMax = new Date(document.getElementById("dateMaxParam").value.replace("/", "-"));
		if(picker == document.getElementById("dateMinParam") && dMin > dMax) {
			document.getElementById("dateMaxParam").value = document.getElementById("dateMinParam").value;
		}
		if(dMax < dMin){
			document.getElementById("dateMinParam").value = document.getElementById("dateMaxParam").value;
		}
	}
	
	
</script>
</html>
