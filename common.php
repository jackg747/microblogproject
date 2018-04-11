<?php
require_once("vendor/password.php");
require_once("validation.php");
require_once("functions.php");

$var = session_start();
if (!$var) { throw new Exception("Sessions could not be started"); }

$config = parse_ini_file('env.ini');
if (!empty($config['FORCE_LOGIN_AS'])) {
    $_SESSION['user_email'] = $config['FORCE_LOGIN_AS'];
}
