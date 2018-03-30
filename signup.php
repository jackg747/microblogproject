<?php require_once("common.php");

$message = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        create_user($_POST);
    } catch (Exception $e) {
        $message = $e->getMessage();
    }
}
?>
<?php require_once("header.php"); ?>

<form id="sign-up" onsubmit="return validateSignUpForm()" method="post" enctype="multipart/form-data">
	<table>
		<tr class= "fieldparent">
			<td>Firstname:</td>
			<td><input type="text" name="first_name" size="31" /></td>
		</tr>
		<tr class= "fieldparent">
			<td>Lastname:</td>
			<td><input type="text" name="last_name" size="31" /></td>
		</tr>
		<tr class= "fieldparent">
			<td>Date of Birth:</td>
			<td><input type="text" name="date_of_birth" size="31" /></td>
		</tr>
		<tr class= "fieldparent">
			<td>Plane Owned:</td>
			<td><input type="text" name="plane_owned" size="31" /></td>
		</tr>
		<tr class= "fieldparent">
			<td>Email:</td>
			<td><input type="text" name="email" size="31" /></td>
		</tr>
		<tr class= "fieldparent">
			<td>Username:</td>
			<td><input type="text" name="username" size="31" /></td>
		</tr>
		<tr class= "fieldparent">
			<td>Image:</td>
			<td><input type="file" name="img" /></td>
		</tr>
		<tr class= "fieldparent">
			<td>Password:</td>
			<td><input type="password" name="password" size="31" /></td>
		<tr class= "fieldparent">
			<td>Confirm Password:</td>
			<td><input type="password" name="confirm_password" size="31" /></td>
		</tr>
	</table>
	<h3>
		<input type="submit" name="SignUp" value="SignUp" /> <input
			type="reset" name="Reset" value="Reset" />
	</h3>
</form>
<p>
	<a href="login.php">Already have an account? Click Here!</a>
</p>
<?php require_once("footer.php"); ?>
