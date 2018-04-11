<?php require_once("common.php");

$action = $_POST['action'];
$postIds = getPostIds();
if (empty($postIds) || empty($action)) {
    http_response_code(204);
    die(json_encode(array(
        'error' => true,
        'message' => "What you talking about Willis?!"
    )));
}

switch ($action) {
    case 'getPostReactionCounts':
        $reactionData = array();
        foreach ($postIds as $id) {
            $reactionData[$id] = array(
                'likes'    => get_post_reactions($id, 1),
                'dislikes' => get_post_reactions($id, -1),
            );
        }

        $output = json_encode($reactionData);
        break;
    case 'getNewPosts':
        try {
            $source = $_POST['source'];

            $posts = false;
            if ($source === 'index') {
                $posts = get_all_posts(50, $postIds);
            } elseif ($source === 'profile') {
                $posts = get_users_posts(
                    array('id' => $_POST['userId']),
                    $postIds
                );
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
        } catch (Exception $ex) {
            $output = $ex->getMessage();
        }

        break;
}

function getPostIds() {
    $postIds = $_POST['postIds'];
    $filteredPostIds = array();

    foreach ($postIds as $id => $use) {
        if ($use) {
            $filteredPostIds[] = $id;
        }
    }

    return $filteredPostIds;
}

echo $output;
die;
