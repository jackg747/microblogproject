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
    }

    if ($error = get_database()->error) {
        throw new Exception($error . ' ' . var_export($query, true));
    }
}

function get_multiple_records($query)
{
    $result = get_database()->query($query);

    $records = array();
    if ($result && $result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $records[] = $row;
        }
    }

    if ($error = get_database()->error) {
        throw new Exception($error . ' ' . var_export($query, true));
    }

    return $records;
}

function create_post($content, $parent_id = false)
{
    // Get the user
    $user = get_user();

    // Validate post content
    $content = trim($content);
    if (empty($content)) {
        display_error("Your post must not be empty.");
    }
    if (strlen($content) > 250) {
        display_error("Your post must not exceed 250 characters.");
    }

    // Build the post data to insert
    $postData = array(
        'user_id'    => $user['id'],
        'content'    => $content,
        'created_at' => date("Y-m-d H:i:s"),
    );
    if (!empty($parent_id)) {
        $postData['parent_id'] = $parent_id;
    }

    // Insert the post
    insert_single_record('posts', $postData);
}

function update_reaction($reactionId, $value)
{
    $value = (int) $value;
    $query = "UPDATE `reactions` SET value = $value WHERE id = $reactionId;";

    $result = get_database()->query($query);

    if ($error = get_database()->error) {
        throw new Exception($error . ' ' . var_export($query, true));
    }

    return $result;
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

function get_post_by_id($id)
{
    $id = (int) $id;

    $post = get_single_record("SELECT * FROM posts WHERE id = $id");
    if ($post) {
        $post['user'] = get_user_by_id($post['user_id']);
    }

    return $post;
}

function get_all_posts($limit = 0, $exclude = false)
{
    $query = "SELECT id from posts ";
    if (!empty($exclude)) {
        $query .= "WHERE ID NOT IN ('" . implode("', '", $exclude) . "') ";
    }
    $query .= "ORDER BY created_at DESC";
    if ($limit) {
        $query .= " LIMIT $limit";
    }

    $postIds = get_multiple_records($query);

    $posts = array();
    if (!empty($postIds)) {
        foreach ($postIds as $id) {
            $posts[] = get_post_by_id($id['id']);
        }
    }

    return $posts;
}

function get_users_posts($user, $exclude = '')
{
    $id = (int) $user['id'];

    if (!empty($exclude)) {
        $exclude = " AND id NOT IN ('" . implode("', '", $exclude) . "')";
    }

    $query = "SELECT id FROM posts where user_id = $id $exclude ORDER BY `created_at` DESC";

    $posts = array();
    $postIds = get_multiple_records($query);

    if (!empty($postIds)) {
        foreach ($postIds as $id) {
            $posts[] = get_post_by_id($id['id']);
        }
    }

    return $posts;
}

function get_user_by_id($id)
{
    $id = (int) $id;

    $user = get_single_record("SELECT * FROM users WHERE id = $id");

    return $user;
}

function get_user_by_email($email)
{
    $user = get_single_record("SELECT * FROM users WHERE email = '$email'");

    if (empty($user)) {
        $user = false;
    }

    return $user;
}

function get_user_by_username($username)
{
    $user = get_single_record("SELECT * FROM users WHERE username = '$username'");

    if (empty($user)) {
        $user = false;
    }

    return $user;
}

function get_user($force = false)
{
    global $user;
    if ($force || (empty($user) && !empty($_SESSION['user_email']))) {
        $user = @get_user_by_email($_SESSION['user_email']);
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


function get_user_link($user)
{
    return "<a href='profile.php?user={$user['username']}'>@{$user['username']}</a>";
}


function get_user_profile_img($user)
{
    if (empty($user['profile_photo'])) {
        return 'cessna.jpg';
    } else {
        return $user['profile_photo'];
    }
}

function get_user_displayname($user)
{
    return $user['first_name'] . ' ' . $user['last_name'];
}


function display_post($post, $hideActions = false)
{
    if (empty($post)) {
        return;
    }

    $repost = false;
    if ($post['parent_id']) {
        $repost = get_post_by_id($post['parent_id']);
    }
    ?>

    <div class="post">
        <?php if (!empty($repost)) { ?>
            <div class="repost">
                <span>
                    <?php echo get_user_link($post['user']); ?>
                    reposted from <?php echo get_user_link($repost['user']); ?>
                </span>
            </div>
        <?php } ?>

        <div data-post-id="<?php echo $post['id']; ?>">
            <?php display_post_content($post, $hideActions); ?>
        </div>

        <?php if (!empty($repost)) { ?>
            <div data-post-id="<?php echo $repost['id']; ?>" style="margin-left:1em;margin-top:1em;background:#aaa;padding:2em;">
                <?php display_post_content($repost, $hideActions); ?>
            </div>
        <?php } ?>
    </div>
    <?php
}

function display_post_content($post, $hideActions)
{
    ?>
    <div class="userinfo">
        <img class="userpostprofile" src="<?php echo get_user_profile_img($post['user']); ?>" alt="<?php echo get_user_displayname($post['user']); ?>" />
        <span class="postmeta"><?php echo get_user_link($post['user']); ?> - <?php echo date('H:i - F jS, Y', strtotime($post['created_at'])); ?></span>
    </div>
    <div class="text">
        <p><?php echo htmlspecialchars($post['content']); ?></p>
    </div>
    <div class="postaction">
        <?php if (!$hideActions) { ?>
            <div class="repostcontainer">
                <a class="repostbutton" href="newpost.php?repost=<?php echo $post['id']; ?>"> Repost</a>
            </div>

            <div class="counter">
                <a class="likebutton <?php get_reaction_class($post['id'], 1); ?>" href="javascript:;" data-post-id=<?php echo $post['id']; ?>> +1 (<span class="reaction-counter"><?php echo get_post_reactions($post['id'], 1); ?></span>)</a>
                <a class="dislikebutton <?php get_reaction_class($post['id'], -1); ?>" href="javascript:;"  data-post-id=<?php echo $post['id']; ?>> -1 (<span class="reaction-counter"><?php echo get_post_reactions($post['id'], -1); ?></span>)</a>
            </div>
        <?php } ?>
    </div>
    <?php
}

function get_reaction_class($postId, $value)
{
    $user = get_user();

    if ($user) {
        $reaction = get_single_record("SELECT id FROM `reactions` WHERE post_id = $postId AND user_id = {$user['id']} AND value = $value");
        if (!empty($reaction)) {
            echo 'clicked';
        }
    }
}

function get_post_reactions($postId, $value)
{
    $postId = (int) $postId;
    $reactions = get_single_record("SELECT COUNT(*) as numOfReactions FROM `reactions` WHERE post_id = $postId AND value = $value");
    return @$reactions['numOfReactions'];
}

if (!function_exists('http_response_code')) {
    function http_response_code($code = NULL) {
        if ($code !== NULL) {
            switch ($code) {
                case 100: $text = 'Continue'; break;
                case 101: $text = 'Switching Protocols'; break;
                case 200: $text = 'OK'; break;
                case 201: $text = 'Created'; break;
                case 202: $text = 'Accepted'; break;
                case 203: $text = 'Non-Authoritative Information'; break;
                case 204: $text = 'No Content'; break;
                case 205: $text = 'Reset Content'; break;
                case 206: $text = 'Partial Content'; break;
                case 300: $text = 'Multiple Choices'; break;
                case 301: $text = 'Moved Permanently'; break;
                case 302: $text = 'Moved Temporarily'; break;
                case 303: $text = 'See Other'; break;
                case 304: $text = 'Not Modified'; break;
                case 305: $text = 'Use Proxy'; break;
                case 400: $text = 'Bad Request'; break;
                case 401: $text = 'Unauthorized'; break;
                case 402: $text = 'Payment Required'; break;
                case 403: $text = 'Forbidden'; break;
                case 404: $text = 'Not Found'; break;
                case 405: $text = 'Method Not Allowed'; break;
                case 406: $text = 'Not Acceptable'; break;
                case 407: $text = 'Proxy Authentication Required'; break;
                case 408: $text = 'Request Time-out'; break;
                case 409: $text = 'Conflict'; break;
                case 410: $text = 'Gone'; break;
                case 411: $text = 'Length Required'; break;
                case 412: $text = 'Precondition Failed'; break;
                case 413: $text = 'Request Entity Too Large'; break;
                case 414: $text = 'Request-URI Too Large'; break;
                case 415: $text = 'Unsupported Media Type'; break;
                case 500: $text = 'Internal Server Error'; break;
                case 501: $text = 'Not Implemented'; break;
                case 502: $text = 'Bad Gateway'; break;
                case 503: $text = 'Service Unavailable'; break;
                case 504: $text = 'Gateway Time-out'; break;
                case 505: $text = 'HTTP Version not supported'; break;
                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                break;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            header($protocol . ' ' . $code . ' ' . $text);
            $GLOBALS['http_response_code'] = $code;
        } else {
            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
        }
        return $code;
    }
}
