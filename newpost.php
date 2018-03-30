<?php require_once("common.php");

if (!get_user()) {
    header('Location: index.php');
}

$repost = false;
if ($_GET['repost']) {
    $repost = get_post_by_id($_GET['repost']);
}

$message = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        create_post($_POST['postContent'], @$repost['id']);
        header('Location: index.php');
    } catch (Exception $e) {
        $message = $e->getMessage();
    }
}
?>
<?php require_once("header.php"); ?>
    <script src="post.js"></script>

    <?php if ($repost) { ?>
        <span>Reposting:</span>
        <?php display_post($repost, true); ?>
    <?php } ?>

    <form id="post" onsubmit="return validatePostForm()" method="post">
        <div class= "fieldparent">
            <textarea style="display:block;height:70px;width:100%;max-width:300px;margin-bottom:1em;" name="postContent"></textarea>
            <span style="display:block;margin-bottom:1em;" class="characterMessage">Characters: <span class = "characterCounter">0</span>/250 Remaining: <span class = 'remainingCharacters'>250</span></span>
        </div>
        <input type="submit" name="Post" value="Post" />
        <input type="reset"	name="Reset" value="Reset" />
    </form>
</body>
<?php require_once("footer.php"); ?>
