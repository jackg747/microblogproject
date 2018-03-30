<?php require_once("header.php"); ?>
    <h2>AvGeek's Posts!</h2>
    <?php if (get_user()) { ?>
        <a href="newpost.php">Make a new post</a>
    <?php } else { ?>
        <a href="login.php">Login</a><br>
        <a href="signup.php">Signup</a><br>
    <?php } ?>

    <h4>Posts</h4>
    <?php
        $posts = get_all_posts(20);
        if (!empty($posts)) {
            foreach ($posts as $post) {
                display_post($post);
            }
        } else {
            echo 'No posts to display';
        }
    ?>
<?php require_once("footer.php"); ?>
