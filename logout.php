<!DOCTYPE html>
<html lang="en">
<head>
    <title>Log Out</title>
    <meta charset="utf-8"/>
    <link rel="stylesheet" href="theme.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
</head>
<body>
    <?php
    session_start();
    session_destroy();
    ?>
    <div class="container4">
    <p>You logged out!</p>
    <form action="login.html">
        <input type="submit" value="Log in" id="login"/>
    </form>
    </div>
</body>
<script>
    $("#login").button();
</script>
</html>
