<?php require_once('common.php');
    if ($username = @$_GET['user']) {
        $pageUser = get_user_by_username($username);
    } elseif ($user = get_user()) {
        $user = $user;
    } else {
        header('Location: login.php');
    }

    $dob        = $pageUser['date_of_birth'];
    $name       = get_user_displayname($pageUser);
    $email      = $pageUser['email'];
    $plane      = $pageUser['plane_owned'];
    $username   = $pageUser['username'];
    $profileSrc = get_user_profile_img($pageUser);
?>
<?php require_once("header.php"); ?>
    <h2>@<?php echo $username; ?>'s Page!</h2>
    <div class="profileheader">
        <div class="bio">
            <h3 class="userid">Name: <?php echo $name; ?></h3>
            <table>
                <tbody>
                    <tr>
                        <td>Birthday: <?php echo $dob; ?></td>
                    </tr>
                    <tr>
                        <td>Email: <?php echo $email; ?></td>
                    </tr>
                    <tr>
                        <td>Plane Owned: <?php echo $plane; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="pic">
            <img src="<?php echo $profileSrc; ?>" alt="<?php echo $name; ?>'s Profile Picture"
                style="display: center" width="200" height="200" />
        </div>
    </div>
    <h4>Posts</h4>

    <?php
        $posts = get_users_posts($pageUser);
        if (!empty($posts)) {
            foreach ($posts as $post) {
                display_post($post);
            }
        } else {
            echo get_user_displayname($pageUser) . ' has not made any posts yet.';
        }
    ?>
<?php require_once("footer.php"); ?>
