<?php require_once("header.php"); ?>
    <h2>AvGeek's Posts!</h2>
    <h4>Posts</h4>
    <?php
        $posts = get_all_posts();
        if (!empty($posts)) {
            foreach ($posts as $post) {
                display_post($post);
            }
        } else {
            echo 'No posts to display';
        }
    ?>
<?php require_once("footer.php"); ?>
