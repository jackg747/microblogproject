<?php require_once("common.php");

$message = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        authenticate($_POST['email'], $_POST['password']);
    } catch (Exception $e) {
        $message = $e->getMessage();
    }
}

if (get_user()) {
    header('Location: posts.php');
}
?>
<?php require_once("header.php"); ?>
    <form id="login" onsubmit="return validateLogInForm()" method="post">
        <table>
            <tr class= "fieldparent">
                <td>Email:</td>
                <td>
                    <input type="email" name="email" size="35" />
                </td>
            </tr>
            <tr class= "fieldparent">
                <td>Password:</td>
                <td><input type="password" name="password" size="35" /></td>
            </tr>
        </table>
        <h3>
            <input type="submit" name="Login" value="Login" /> <input
                type="reset" name="Reset" value="Reset" />
        </h3>
    </form>
    <p>
        <a href="signup.php">Don't have an account? Sign-up here!</a>
    </p>
<?php require_once("footer.php"); ?>
