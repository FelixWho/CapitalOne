<!DOCTYPE html>
<html lang="en">
<head>
    <title>Jeopardy Game Board</title>
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

<table id = "tab" class="table" width="100%" height="100%">
    <!--Populate with Jeopardy board-->
</table>

</body>
<script>
    $("#back, #logout, #login").button();

    document.addEventListener("DOMContentLoaded", displayTable, false);

    let tablePop;

    function displayTable(){
        //set table header
        let tab = document.getElementById("tab");
        
        conglomerate()
        .then(content => {
            tablePop = content;
            let tr = document.createElement("TR");
            for(let i = 0; i < 6; i+=1){
                let th = document.createElement("TH");
                th.setAttribute("width", "16.67%");
                th.textContent = content[i].key;
                tr.appendChild(th);
            }
            tab.appendChild(tr);

            for(let i = 0; i < 5; i+=1){
                let tr = document.createElement("TR");

                for(let j = 0; j < 6; j+=1){
                    let div = document.createElement("DIV");
                    let td = document.createElement("TD");
                    td.setAttribute("width", "(100/6)%");
                    div.textContent = (i+1)*100;
                    let question = content[j].value[i];
                    div.addEventListener("click", function(event){
                        alert(question.question);
                        alert("What is "+ question.answer);
                        event.target.textContent="";  
                    }, false);

                    td.appendChild(div);
                    tr.appendChild(td);
                }

                tab.appendChild(tr);
            }
        })
    }

    function getRandom(){ // get six random questions through api

		let url = "http://jservice.io/api/random?count=6";

		// call jService random api
		return fetch(url)
		.then(response => response.json())
		.then(content => {
			return content;
		})
		.catch((err) => {
			console.log(err);
		})		
	}

    function questionToCategory(){ // get categories of questions
        return getRandom()
        .then(content => {
            let six = JSON.parse(JSON.stringify(content)); 
            let ret = [];
            for (let key in six){
			    if(six.hasOwnProperty(key)){
                    let question = six[key];
                    ret.push(question.category.id);
                }
            }
            return ret;
        });
    }

    function questionsFromEachCategory(){ // given 6 categories, return 5 questions with differing value
        return questionToCategory()
        .then(content => {
            let url1 = "http://jservice.io/api/clues?category="+content[0];
            let url2 = "http://jservice.io/api/clues?category="+content[1];
            let url3 = "http://jservice.io/api/clues?category="+content[2];
            let url4 = "http://jservice.io/api/clues?category="+content[3];
            let url5 = "http://jservice.io/api/clues?category="+content[4];
            let url6 = "http://jservice.io/api/clues?category="+content[5];

            return Promise.all([questionFromCategory(url1),
                                questionFromCategory(url2),
                                questionFromCategory(url3),
                                questionFromCategory(url4),
                                questionFromCategory(url5),
                                questionFromCategory(url6)]);
        })
    }

    function questionFromCategory(url){ // get some questions for this category url
        return fetch(url)
        .then(content => content.json())
        .then(content => {
            let questions = JSON.parse(JSON.stringify(content));
            return questions;
        });
    }

    function conglomerate(){ // get exactly 5 questions per 6 categories with sorted values
        return questionsFromEachCategory()
        .then(content => {
            let uniqueInCategory = [];
            for(let key in content){
                if(content.hasOwnProperty(key)){
                    let catQuestions = content[key];
                    let uniqueValue = [];
                    let included = [];
                    $.each(catQuestions, function(index, val) {
                        if(included.length >= 5) {return false;}
                        if ($.inArray(val.value, uniqueValue) === -1 || !val.value) {
                            uniqueValue.push(val.value);
                            included.push(val);
                        }
                    });
                    uniqueInCategory.push({
                        key: catQuestions[0].category.title,
                        value: included
                    });
                }
            }
            return uniqueInCategory;
        });
    }

</script>
</html>