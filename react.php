<?php require_once("common.php");

$user = get_user();
if (!$user) {
    die("You shall not pass!");
}

$postId = (int) $_POST['postId'];
$action = $_POST['action'];
if (empty($postId) || empty($action)) {
    die("What you talking about Willis?!");
}

$value = 0;
switch ($action) {
    case 'like':
        $value = 1;
        break;
    case 'dislike':
        $value = -1;
        break;
}

$reaction = get_single_record("SELECT * FROM reactions WHERE user_id = {$user['id']} AND post_id = $postId");
if ($reaction) {
    update_reaction($reaction['id'], $value);
} else {
    insert_single_record('reactions', array(
        'post_id' => $postId,
        'user_id' => $user['id'],
        'value'   => $value,
    ));
}

echo json_encode(array(
    '-1' => (int) get_post_reactions($postId, -1),
    '1' => (int) get_post_reactions($postId, 1),
));
