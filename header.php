<?php
ini_set('error_reporting', E_ALL);
try {
    require_once("common.php");
} catch (Exception $e) {
    $message = $e->getMessage();
} ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="ISO-8859-1">
        <title>AvGeeks</title>
        <script src="validation.js"></script>
        <script src="jquery-3.3.1.min.js"></script>
        <link rel="stylesheet" type="text/css" href="presentation.css" />
    </head>
    <body>
        <?php if ($loggedInUser = get_user()) { ?>
            <script src="reactions.js"></script>
            <span style="background: black; color: white; display: block; float: right; padding: 1em">
                Welcome <?php echo get_user_displayname($loggedInUser); ?><br>
                <a href="profile.php?user=<?php echo $loggedInUser['username']; ?>">View profile</a>
            </span>
        <?php } else { ?>
            <header>
                <a href="signup.php">Don't have an account? Sign-up here!</a>
            </header>
        <?php } ?>

        <?php if (@$message) { ?>
            <div style="padding: 1em; color: white; background: red;"><?php echo $message; ?></div>
        <?php } ?>
        <a href="index.php" style="display: block;">
            <img src="avgeek.png" alt="Aviation GeekS!" style="display: inline"
                width="200" height="150" />
        </a>
