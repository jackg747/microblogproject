<?php

$database = null;
$user = null;

function get_database()
{
    global $database;

    if (is_null($database)) {
        $config = parse_ini_file('env.ini');

        $database = new mysqli($config['MYSQL_HOST'], $config['MYSQL_USER'], $config['MYSQL_PASS'], $config['MYSQL_DB']);

        // Check connection
        if ($database->connect_error) {
            die("Connection failed: " . $database->connect_error);
        }
    }

    return $database;
}

function get_single_record($query)
{
    $result = get_database()->query($query);

    if ($result && $result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            return $row;
        }
    } else {
        return false;
    }
}

function insert_single_record($table, $data, $allowedColumns = [])
{
    if (!empty($allowedColumns)) {
        foreach ($data as $key => $value) {
            if (!in_array($key, $allowedColumns)) {
                unset($data[$key]);
            }
        }
    }

    $columns = implode(', ', array_keys($data));
    $values = '"' . implode('","', array_values($data)) . '"';

    $query = "INSERT INTO `$table` ({$columns}) VALUES ({$values});";

    $result = get_database()->query($query);

    if ($error = get_database()->error) {
        throw new \Exception($error . ' ' . var_export($query, true));
    }

    return $result;
}

function get_user_by_email($email)
{
    $user = get_single_record("SELECT * FROM users WHERE email = '$email'");

    if (!empty($user[0])) {
        $user = $user[0];
    } else {
        $user = false;
    }

    return $user;
}

function get_user()
{
    global $user;
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
    if (!validate_dateofbirth($data['date_of_birth'])) {
        display_error("Date of birth must follow DD/MM/YYYY.");
    }
    if (!validate_email($data['email'])) {
        display_error("Email provided is invalid.");
    }

    // Check to see if the user already exists
    $user = get_user_by_email($data['email']);
    if ($user) {
        display_error("{$data['email']} already has an account");
    }

    // Hash the password for securely storing it in the DB
    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

    // Insert the user's data
    $success = insert_single_record('users', $data, [
        'first_name',
        'last_name',
        'email',
        'username',
        'password',
        'date_of_birth',
        'plane_owned',
    ]);

    header('Location: login.php');

    // Auto login the user
    // $_SESSION['user_email'] = $data['email']
}
