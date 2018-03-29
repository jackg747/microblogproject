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

function insert_single_record($table, $data, $allowedColumns = array())
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
        throw new Exception($error . ' ' . var_export($query, true));
    }

    return $result;
}

function get_user_by_email($email)
{
    $user = get_single_record("SELECT * FROM users WHERE email = '$email'");

    if (empty($user)) {
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
        throw new Exception("$email does not have a user account");
    }

    // Attempt to verify the provided password against the stored user account
    if (!password_verify($password, $user['password'])) {
        throw new Exception("Password incorrect");
    }

    // Store the user's email in the session
    $_SESSION['user_email'] = $email;

    return $user;
}

function create_user($data)
{
    // Validate provided data
    if (!validate_username(@$data['username'])) {
        display_error("Username cannot contain spaces or special characters.");
    }
    if (!validate_password(@$data['password'])) {
        display_error("Password must be at least 8 characters long and have at least 1 number or symbol.");
    }
    if (@$data['password'] !== @$data['confirm_password']) {
        display_error("Passwords must match");
    }
    if (!validate_dateofbirth(@$data['date_of_birth'])) {
        display_error("Date of birth must follow DD/MM/YYYY.");
    }
    if (!validate_email(@$data['email'])) {
        display_error("Email provided is invalid.");
    }

    // Check to see if the user already exists
    $user = get_user_by_email(@$data['email']);
    if ($user) {
        display_error("{$data['email']} already has an account");
    }

    // Hash the password for securely storing it in the DB
    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

    $picturePath = store_uploaded_files($_FILES['img'], array('jpg', 'jpeg', 'gif', 'png')) ?: array();

    if (!empty($picturePath)) {
        $data['profile_photo'] = 'uploads/' . basename($picturePath[0]);
    }

    // Insert the user's data
    $success = insert_single_record('users', $data, array(
        'first_name',
        'last_name',
        'email',
        'username',
        'password',
        'date_of_birth',
        'plane_owned',
        'profile_photo',
    ));

    header('Location: login.php');

    // Auto login the user
    // $_SESSION['user_email'] = $data['email']
}









// Store uploaded files
function store_uploaded_files($uploaded_files, $allowed_extensions = array()) {
    $final_file_paths = array();

    if (!is_array($uploaded_files['name'])) {
        $uploaded_files['name'] = array($uploaded_files['name']);
        $uploaded_files['tmp_name'] = array($uploaded_files['tmp_name']);
    }

    // Loop through the provided files
    for ($i = 0; $i < count($uploaded_files['name']); $i++) {
        // Get the temporary file path
        $tmp_file_path = $uploaded_files['tmp_name'][$i];

        // Verify that we want to continue processing this file
        if (!empty($tmp_file_path)) {
            // Get the path that the file should be uploaded to
            $upload_file_path = get_upload_path(pathinfo($uploaded_files['name'][$i], PATHINFO_BASENAME));

            // Filter through provided files based on allowed extensions if any were provided
            if (!empty($allowed_extensions)) {
                // Verify that the file passes the extensions check
                if (!empty($tmp_file_path)) {
                    if (in_array(pathinfo($upload_file_path, PATHINFO_EXTENSION), $allowed_extensions)) {
                        if (move_uploaded_file($tmp_file_path, $upload_file_path)) {
                            $final_file_paths[] = $upload_file_path;
                        }
                    }
                }
            } else {
                // Store the file regardless of extension
                if (move_uploaded_file($tmp_file_path, $upload_file_path)) {
                    $final_file_paths[] = $upload_file_path;
                }
            }
        }
    }

    if (!empty($final_file_paths)) {
        return $final_file_paths;
    } else {
        return false;
    }
}

// Build the upload path
function get_upload_path($filename) {
    global $config;

    $upload_dir = __DIR__ . '/uploads/';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    $upload_path = $upload_dir . $filename;

    // Try to make the filename unique
    $i = 0;
    while (file_exists($upload_path) && $i < 5) {
        $i++;
        $upload_path = $upload_dir . pathinfo($upload_path, PATHINFO_FILENAME) . '-' . $i . '.' . pathinfo($upload_path, PATHINFO_EXTENSION);
    }

    return $upload_path;
}
