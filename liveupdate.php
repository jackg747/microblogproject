<?php require_once("common.php");


$action = $_POST['action'];
$postIds = getPostIds();
if (empty($postIds) || empty($action)) {
    http_response_code(204);
    die(json_encode(array('error' => true, 'message' => "What you talking about Willis?!")));
}


switch ($action) {
    case 'getPostReactionCounts':
        $reactionData = [];
        foreach ($postIds as $id) {
            $reactionData[$id] = array(
                'likes'    => get_post_reactions($id, 1),
                'dislikes' => get_post_reactions($id, -1),
            );
        }

        $output = json_encode($reactionData);
        break;
    case 'getNewPosts':
        $source = $_POST['source'];

        if ($source === 'index') {
            $posts = get_all_posts(50, $postIds);
        } elseif ($source === 'profile') {
            $posts = get_users_posts(array('id' => $_POST['userId']), $postIds);
        }

        if (!empty($posts)) {
            ob_start();
            foreach ($posts as $post) {
                display_post($post);
            }
            $output = ob_get_clean();
        } else {
            http_response_code(204);
            $output = json_encode(array('error' => true, 'message' => 'No new posts'));
        }

        break;
}

function getPostIds() {
    $postIds = $_POST['postIds'];
    $filteredPostIds = [];

    foreach ($postIds as $id => $use) {
        if ($use) {
            $filteredPostIds[] = $id;
        }
    }

    return $filteredPostIds;
}

echo $output; die;




// echo json_encode(null);
//
//
//
// $postId = (int) $_POST['postId'];
// $action = $_POST['action'];
// if (empty($postId) || empty($action)) {
//     die("What you talking about Willis?!");
// }
//
// $value = 0;
// switch ($action) {
//     case 'like':
//         $value = 1;
//         break;
//     case 'dislike':
//         $value = -1;
//         break;
// }
//
// $reaction = get_single_record("SELECT * FROM reactions WHERE user_id = {$user['id']} AND post_id = $postId");
// if ($reaction) {
//     if ((string) $reaction['value'] === (string) $value) {
//         $value = 0;
//     }
//
//     update_reaction($reaction['id'], $value);
// } else {
//     insert_single_record('reactions', array(
//         'post_id' => $postId,
//         'user_id' => $user['id'],
//         'value'   => $value,
//     ));
// }
//
// echo json_encode(array(
//     '-1' => (int) get_post_reactions($postId, -1),
//     '1' => (int) get_post_reactions($postId, 1),
// ));
