<?php require_once("common.php");

$message = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        create_user($_POST);
    } catch (\Exception $e) {
        $message = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="ISO-8859-1">
<title>Sign-Up for AvGeek News Here!</title>
<script src="validation.js"></script>
<script src="jquery-3.3.1.min.js"></script>
<link rel="stylesheet" type="text/css" href="presentation.css" />
</head>
<body>
	<h1>
		<img src="avgeek.png" alt="Aviation GeekS!" style="display: inline"
			width="200" height="150" /> Sign Up Page
	</h1>

    <?php if ($message) { ?>
        <div style="padding: 1em; color: white; background: red;"><?php echo $message; ?></div>
    <?php } ?>

	<form id="sign-up" onsubmit="return validateSignUpForm()" method="post">
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
		<a href="login.html">Already have an account? Click Here!</a>
	</p>
</body>
</html>
