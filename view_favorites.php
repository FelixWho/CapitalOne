<!DOCTYPE html>
<html lang="en">
<head>
    <title>Favorite Questions</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">    
    <link rel="stylesheet" href="theme.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
</head>
<body id="body">
<?php
session_start();

if(isset($_SESSION['username'])){
    # logged in
    echo "<div class='container'>";
    echo    "<div>";
    echo 	    "<form action = 'search.php'>";
    echo 		    "<input type='submit' value='Back' id='back'/>";
    echo 	    "</form>";
    echo    "</div>";
    echo    "<div>";
    echo 	    "<form action = 'logout.php'>";
    printf("<input type='submit' value='Log out %s' id='logout'>", htmlentities($_SESSION['username']));
    echo 	    "</form>";
    echo    "</div>";
    echo "</div>";
} else {
    # not logged in
    echo "<div class='container'>";
    echo    "<div>";
    echo 	    "<form action = 'search.php'>";
    echo 		    "<input type='submit' value='Back' id='back'/>";
    echo 	    "</form>";
    echo    "</div>";
    echo    "<div><form action = 'login.html'>";
    echo    "<input type='submit' value='Log in' id='login'/>";
    echo "</form></div>";
    echo "</div>";
}
?>

<div id="results">
	<!--will populate with favorites table-->
</div>
</body>
<script>
    $("#back, #logout, #login").button();

    $("#results").accordion({ header: "h3", collapsible: true, active: false });

    document.addEventListener("DOMContentLoaded", displayFavorites, false);

    function displayFavorites(event){
        // clear table UI
        let tab = document.getElementById("results");
        if(tab) { tab.innerHTML=""; }
        
        fetch("get_favorites.php")
        .then(content => content.json())
        .then(content => {
            let loggedIn = <?php if(isset($_SESSION['username'])) {echo "1";} else {echo "0";} ?>;
            let favorites = JSON.parse(JSON.stringify(content));
            for(let key in favorites){
                if(favorites.hasOwnProperty(key)){
                    let question = favorites[key];
                    let que = document.createElement("H3");
				    let ans = document.createElement("DIV");

                    que.id = question.id;
                    que.innerText = question.question;
                    ans.id = question.id;
                    ans.innerHTML = "<p>Answer: What is "+question.answer+"</p>"
								+ "<p>Date Aired: "+new Date(question.airdate).toLocaleDateString("en-US")+"</p>"
								+ "<p>Value: "+question.value+"</p>"
								+ "<p>Category: "+question.category.title+"</p>";

                    if(loggedIn == "1") { // add favorite button
                        let favoriteButton = document.createElement("input");
                        favoriteButton.type = "submit"; favoriteButton.name = question.q_id;
                        favoriteButton.value = "Unfavorite";
                        favoriteButton.addEventListener("click", function listen(event){
                            deleteFavorite(event);
                            event.target.parentNode.previousSibling.remove();
                            event.target.parentNode.remove();
                        }, false);
                        ans.appendChild(favoriteButton);
				    }
				tab.appendChild(que);
				tab.appendChild(ans);
                }
            }
            $("#results").accordion("refresh");
        })
        .catch((err) => {
            console.log(err);
        });
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
</script>
</html>