<?php

function display_error($message) {
    throw new \Exception($message);
}

function validate_email($email) {
    return (bool) preg_match("/\S+@\S+\.\S+/", $email);
}

function validate_password($password) {
    if (strlen($password) >= 8 && preg_match("/^[a-z]+$/i", $password)) {
        return false;
    }
    return true;
}

function validate_dateofbirth($dateofbirth) {
    return (bool) preg_match("/^\d{1,2}\/\d{1,2}\/\d{4}$/", $dateofbirth);
}

function validate_username($username) {
    if (preg_match("/^\S*$/", $username) && preg_match("/^\w+$/", $username)) {
        return true;
    }
    return false;
}
