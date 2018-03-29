<?php
require_once("vendor/Medoo.php");
require_once("validation.php");

$database = null;
$user = null;

function get_database()
{
    global $database;

    if (is_null($database)) {
        $config = parse_ini_file('env.ini');

        $database = new Medoo([
            'database_type' => 'mysql',
            'database_name' =>  $config['MYSQL_DB'],
            'server' => $config['MYSQL_HOST'],
            'username' => $config['MYSQL_USER'],
            'password' => $config['MYSQL_PASS'],
        ]);
    }

    return $database;
}

function get_user_by_email($email)
{
    $user = get_database()->select('users', '*', [
        'email[>]' => $email,
    ]);

    if (!empty($user[0])) {
        $user = $user[0];
    } else {
        $user = false;
    }

    return $user;
}

function get_user()
{
    global $user
    if (empty($user) && !empty($_SESSION['email'])) {
        $user = get_user_by_email($_SESSION['email']);
    }

    return $user;
}

function authenticate($email, $password)
{
    global $user;

    // Attempt to retrieve the selected user
    $user = get_user_by_email($email);
    if (!$user) {
        throw new \Exception("$email does not have a user account");
    }

    // Attempt to verify the provided password against the stored user account
    if (!password_verify($password, $user['password'])) {
        throw new \Exception("Password incorrect");
    }

    // Store the user's email in the session
    $_SESSION['user_email'] = $email;

    return $user;
}

function create_user($data)
{
    // Validate provided data
    if (!validate_username($data['username'])) {
        display_error("Username cannot contain spaces or special characters.");
    }
    if (!validate_password($data['password'])) {
        display_error("Password must be at least 8 characters long and have at least 1 number or symbol.");
    }
    if ($data['password'] !== $data['confirm_password']) {
        display_error("Passwords must match");
    }
    if (!validate_dateofbirth($data['date_of_birth']) {
        display_error("Date of birth must follow DD/MM/YYYY.");
    }
    if (!validate_email($data['email'])) {
        display_error("Email provided is invalid.");
    }

    // Check to see if the user already exists
    $user = get_user_by_email($data['email']);
    if ($user) {
        throw new \Exception("{$data['email']} already has an account");
    }

    // Hash the password for securely storing it in the DB
    $data['password'] = password_hash($data['password']);

    // Insert the user's data
    get_database()->insert('users', $data);

    // Auto login the user
    // $_SESSION['user_email'] = $data['email']
}
