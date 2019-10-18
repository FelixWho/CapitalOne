<!DOCTYPE html>
<html lang="en">
<head>
        <title>Log Out</title>
        <meta charset="utf-8"/>
</head>
<body>
    <?php
    session_start();
    session_destroy();
    ?>
    <p>You logged out!</p>
    <form action="login.html">
        <input type="submit" value="Log in" />
    </form>
</body>
</html>
