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
<!--<label for="dateMinParam">Minimum Date Aired:</label>-->
<input type="text" id = "dateMinParam" placeholder="Pick minimum date" readonly>
</div>
<div>
<!--<label for="dateMaxParam">Maximum Date Aired:</label>-->
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

	/******
                GLOBAL VARIABLES
    ******/

	let catID = {}; // dictionary holding category-id pairs

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
        // clear table UI
        let tab = document.getElementById("results");
        if(tab) { tab.remove(); }

		// table to display search results -- should move inside fetch
		let body = document.getElementById("body");
		let results = document.createElement("TABLE");
        results.id = "results";

		search(offset).then(content => {
			for(ret in content){
				let arr = JSON.parse(JSON.stringify(ret)); 
				for (let key in arr){
					if(arr.hasOwnProperty(key)){
						let question = arr[key];

						let tr = document.createElement("TR");
						let td = document.createElement("TD");

						tr.id = question.id; td.id = question.id;
						td.textContent = question.question;

						tr.appendChild(td);
						results.appendChild(tr);
					}
				}
				body.appendChild(results);
			}
		});
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

		if(catID !== undefined && Object.keys(catID).length > 0){
			// multiple possible categories that user intends; we'll call all of them
			for(let entry in catID){
				if (catID.hasOwnProperty(entry)) { 
					let tempURL = url.concat("&category="+encodeURIComponent(catID[entry]));
					callURLs.push(tempURL);
				}
			}
		} else if(Object.keys(catID).indexOf(category) > -1){
			// there is exactly one specified category
			let tempURL = url.concat("&category="+encodeURIComponent(catID[category]));
			callURLs.push(tempURL);
		} else {
			// include URL without any category specified
			callURLs.push(url);
		}

		console.log("search:");
		console.log(Object.keys(catID));
		callURLs.forEach(item => console.log(item));
		return Promise.all(callURLs.map(u=>fetch(u))).then(responses =>
    		Promise.all(responses.map(res => res.text()))
		)
		/*
			// call jService clues api
			return fetch(entry)
			.then(response => response.json())
			.then(content => {
				return content;
			})
			.catch((err) => {
				console.log(err);
			})		
			*/
	}

	function fetchCategories(event){ // call jService search API
		// call search api
		console.log("fetching categories with: "+ "http://jservice.io/search?query="+event.target.value.replace(/ /g,"+"));
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
