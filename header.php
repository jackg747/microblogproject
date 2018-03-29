<?php session_start(); require_once("common.php"); ?>
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
        <?php if (@$message) { ?>
            <div style="padding: 1em; color: white; background: red;"><?php echo $message; ?></div>
        <?php } ?>
