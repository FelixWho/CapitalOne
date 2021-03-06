<!DOCTYPE html>
<html lang="en">
<head>
    <title>Jeopardy Search Engine</title>
    <meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">	
	<link rel="stylesheet" href="theme.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
</head>
<body id = "body">

<?php
session_start();
if(isset($_SESSION['username'])){
	# logged in
	echo "<div class='container'>";
	echo    "<div>";
    echo 	    "<form action = 'jeopardy_demo.php'>";
    echo 		    "<input type='submit' value='Game on' id='demo'/>";
    echo 	    "</form>";
    echo    "</div>";
	echo 	"<div class = 'align-left'>";
	echo 		"<form action = 'view_favorites.php'>";
	echo 			"<input type='submit' value='Favorites' id='fav'/>";
	echo 		"</form>";
	echo	"</div>";
	echo	"<div class = 'align-right'>";
	echo 		"<form action = 'logout.php'>";
    printf("<input type='submit' value='Log out %s' id='logout'>", htmlentities($_SESSION['username']));
	echo 		"</form>";
	echo 	"</div>";
	echo "</div>";
} else {
	# not logged in
	echo "<div class='container'>";
	echo    "<div>";
    echo 	    "<form action = 'jeopardy_demo.php'>";
    echo 		    "<input type='submit' value='Game on' id='demo'/>";
    echo 	    "</form>";
    echo    "</div>";
	echo 	"<div><form action = 'login.html'>";
    echo 		"<input type='submit' value='Log in' id='login'/>";
	echo 	"</form></div>";
	echo "</div>";
}
?>
<div class="container2">
	<!--search by difficulty value-->
	<div>
		<input list="values" id = "valueParam" placeholder="Difficulty">
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
		<input list="category" id = "catParam" placeholder="Category">
		<datalist id="category">
			<!--will populate with javascript + webscraping-->
		</datalist>
	</div>
	<div style="text-align:center;">
		<input type="submit" id = "search" value="Search">
	</div>
</div>

<div class="container3">
	<!--search by date-->
	<div>
		<input type="text" id = "dateMinParam" placeholder="Minimum air date" readonly>
	</div>
	<div>
		<input type="text" id = "dateMaxParam" placeholder="Maximum air date" readonly>
	</div>
	<div>
		<input type="submit" id="clearDate" value="Clear dates">
	</div>
</div>

<div id="results">
	<!--will populate with search results-->
</div>

</body>
<script>

/*
                JQUERY UI
				intended for usage across different web browsers
*/

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
	$("#demo, #fav, #logout, #clearDate, #search, #login").button();
    $("#results").accordion({ header: "h3", collapsible: true, active: false });

/*
                GLOBAL VARIABLES
*/

	let catID = {}; // dictionary holding category-id pairs
	let results = {}; // dictionary holding parsed api results

/*
                EVENT LISTENERS
*/

	document.getElementById("search").addEventListener("click", function(event) {displayEvents(0);}, false);
	document.getElementById("dateMinParam").addEventListener("change", dateCheck, false);
	document.getElementById("dateMaxParam").addEventListener("change", dateCheck, false);
	document.getElementById("catParam").addEventListener("input", fetchCategories, false);
	document.getElementById("clearDate").addEventListener("click", clearDate, false);

/*
                FUNCTIONS
                (extra functions may be included as files) 
*/

	function displayEvents(offset){
		if(!checkCategoryBox()){
			return;
		}

        // clear table UI
		let tab = document.getElementById("results");
		if(tab) { tab.innerHTML=""; }
		if(document.getElementById("nextContainer")){
			document.getElementById("nextContainer").remove();
		}

		// check if there is another page, if so add "next page" button
		getSearch(offset+100)
		.then(content => {
			if(Object.entries(content).length === 0){
				console.log("No further pages");
			} else {
				console.log("There is another page");
				let contain = document.createElement("DIV");
				contain.id = "nextContainer";
				contain.className = "container";
				let nextPage = document.createElement("input");
				nextPage.type = "submit"; nextPage.value="Next Page"; nextPage.id = "nextPage";
				nextPage.addEventListener("click", function(event){
					displayEvents(offset+100);
				},false,);
				contain.appendChild(nextPage);
				insertAfter(contain, document.getElementById("results"));
				$("#nextPage").button();
				$("#nextPage").css({"border":0});
			}
		});

		getSearchAndFav(offset).then(([favResults, searchResults]) => {

		let favList = JSON.parse(JSON.stringify(favResults)); 
		let parsedResults = JSON.parse(JSON.stringify(searchResults)); 

		results = parsedResults;
		let loggedIn = <?php if(isset($_SESSION['username'])) {echo "1";} else {echo "0";} ?>;
		for (let key1 in parsedResults){
			if(parsedResults.hasOwnProperty(key1)){
				let question = parsedResults[key1];

				let que = document.createElement("H3");
				let ans = document.createElement("DIV");

				que.id = question.id;
				que.innerText = question.question;
				ans.id = question.id;
				ans.innerHTML = "<p>Answer: What is "+question.answer+"</p>"
								+ "<p>Date Aired: "+new Date(question.airdate).toLocaleDateString("en-US")+"</p>"
								+ "<p>Value: "+question.value+"</p>"
								+ "<p>Category: "+question.category.title+"</p>";
				
				if(loggedIn == "1") { // add favorite/unfavorite buttons
					let isFav = false;
					for(let key2 in favList){ // check if current question is favorited
						if(favList.hasOwnProperty(key2)){
							let q_id = favList[key2].q_id;
							if(q_id == que.id){
								isFav = true;
							}
						}
					}
					let favoriteButton = document.createElement("input");
					favoriteButton.type = "submit"; favoriteButton.name = question.id;
					if(isFav){
				 		favoriteButton.value = "Unfavorite";
						favoriteButton.addEventListener("click", function listen(event){
							event.target.value="Favorite";
							deleteFavorite(event);
							changeButtonToFavoriteButton(event);
						}, false);
					} else {
						favoriteButton.value = "Favorite";
						favoriteButton.addEventListener("click", function listen(event){
							event.target.value="Unfavorite";
							addFavorite(event);
							changeButtonToUnfavoriteButton(event);
						}, false);
					}
					ans.appendChild(favoriteButton);
				}
				tab.appendChild(que);
				tab.appendChild(ans);
			}
		}
		$("#results").accordion("refresh");
		});
	}

	function changeButtonToFavoriteButton(event){ // change an unfav button to fav button after pressed
		let par = event.target.parentNode;
		let id = event.target.name;
		event.target.remove();
		let favoriteButton = document.createElement("input");
		favoriteButton.type="submit"; favoriteButton.name=id; favoriteButton.value="Favorite";
		favoriteButton.addEventListener("click", function(event){
			addFavorite(event);
			changeButtonToUnfavoriteButton(event);
		}, false);
		par.appendChild(favoriteButton);
	}

	function changeButtonToUnfavoriteButton(event){ // change an unfav button to fav button after pressed
		let par = event.target.parentNode;
		let id = event.target.name;
		event.target.remove();
		let favoriteButton = document.createElement("input");
		favoriteButton.type="submit"; favoriteButton.name=id; favoriteButton.value="Unfavorite";
		favoriteButton.addEventListener("click", function(event){
			deleteFavorite(event);
			changeButtonToFavoriteButton(event);
		}, false);
		par.appendChild(favoriteButton);
	}

	function deleteFavorite(event){
		let name = event.target.name;
		let eventObject = {
			"id": name
		}
		fetch("delete_favorite.php", {
			method: 'POST',
			body: JSON.stringify(eventObject),
			headers: { 'content-type': 'application/json' }
		})
		.catch((err) => {
			console.log(err);
		});
	}

	function addFavorite(event){
		let name = event.target.name;
		for(let key in results){
			if(results.hasOwnProperty(key)){
				let question = results[key];
				if(question.id == name){
					// matched the search result; add to favorite
					let eventObject = {
					"id": (!question.id ? -1 : question.id ), 
					"question": (!question.question ? "" : question.question ),
					"answer": (!question.answer ? "" : question.answer ),
					"category": (!question.category.title ? "" : question.category.title ),
					"date_aired": (!question.airdate ? "" : new Date(question.airdate).toSQLFormat()) };
					fetch("add_favorite.php", {
						method: 'POST',
						body: JSON.stringify(eventObject),
						headers: { 'content-type': 'application/json' }
					})
					.catch((err) => {
						console.log(err);
					});
				}
			}
		}

	}

	function checkCategoryBox(){ 
		// helper function that checks if category box has input,
		// and if the input is a valid category
		let box = document.getElementById("catParam");
		if(box.value.length > 0 && Object.keys(catID).indexOf(box.value) == -1){
			// user inputted a category that doesn't exist, alert
			alert("Please input a valid category or select a category from the drop-down menu (non-mobile only).");
			return false;
		}
		return true;
	}

	function getSearchAndFav(offset = 0){
		return Promise.all([getFav(), getSearch(offset)]);
	}

	function getFav(event){
		let loggedIn = <?php if(isset($_SESSION['username'])) {echo "1";} else {echo "0";} ?>;
		let eventObject;
		if(loggedIn == "1"){
			eventObject = {"username": <?php if(isset($_SESSION['username'])) {echo json_encode($_SESSION['username']);} else echo "-1"; ?> };
		} else {
			eventObject = {};
		}
		return fetch("is_favorite.php", {
			method: 'POST',
			body: JSON.stringify(eventObject),
			headers: { 'content-type': 'application/json' }
		})
		.then(response => response.json());
	}

	function getSearch(offset){ // search button pressed
		const val = document.getElementById("valueParam").value;
		const datMin = document.getElementById("dateMinParam").value.replace("/", "-");
		const datMax = document.getElementById("dateMaxParam").value.replace("/", "-");
		const category = document.getElementById("catParam").value;
		
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
			console.log(err);
		});
	}

	function clearDate(event){ // clear date inputs
		let dates = $("input[id$='dateMinParam'], input[id$='dateMaxParam']");
		dates.attr('value', '');
		dates.each(function(){
			$.datepicker._clearDate(this);
		});
	}

	function twoDigits(d) {
		// source: https://stackoverflow.com/questions/5129624/convert-js-date-time-to-mysql-datetime
		if(0 <= d && d < 10) return "0" + d.toString();
		if(-10 < d && d < 0) return "-0" + (-1*d).toString();
		return d.toString();
	}

	Date.prototype.toSQLFormat = function() {
		// convert JS Date type to MySQL date format
		// source: https://stackoverflow.com/questions/5129624/convert-js-date-time-to-mysql-datetime
		return this.getUTCFullYear() + "-" + twoDigits(1 + this.getUTCMonth()) + "-" + twoDigits(this.getUTCDate());
	};

	Date.prototype.toHTMLFormat = function() {
		// convert JS Date type to HTML date format
		return  twoDigits(1 + this.getUTCMonth()) + "/" + twoDigits(this.getUTCDate())+ "/" +this.getUTCFullYear();
	};

	function dateCheck(event){ // ensures min date <= max date
		let in1 = document.getElementById("dateMinParam").value;
		let in2 = document.getElementById("dateMaxParam").value;
		let dMin = new Date(in1.replace("/", "-"));
		let dMax = new Date(in2.replace("/", "-"));
		if(event == document.getElementById("dateMinParam") && dMin > dMax) {
			document.getElementById("dateMaxParam").value = in1;
		} else if(dMax < dMin){
			document.getElementById("dateMinParam").value = in2;
		}

		// fill in other field if empty
		if(event == document.getElementById("dateMinParam") && (in2 == null || in2 == "")){
			if(dMin > new Date()) {
				document.getElementById("dateMaxParam").value = in1;
			} else {
				document.getElementById("dateMaxParam").value = new Date().toHTMLFormat();
			}
		}
		if(event == document.getElementById("dateMaxParam") && (in1 == null || in1 == "")){
			document.getElementById("dateMinParam").value = "01/01/1950";
		}
		
	}

	function insertAfter(newNode, referenceNode) {
		// source: https://stackoverflow.com/questions/4793604/how-to-insert-an-element-after-another-element-in-javascript-without-using-a-lib
		referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
	}
	
	
</script>
</html>
