<!DOCTYPE html>
<html>
    <head>
        <meta charset="ISO-8859-1">
        <title>AvGeeks Login Here!</title>
        <script src="validation.js"></script>
        <script src="jquery-3.3.1.min.js"></script>
        <link rel="stylesheet" type="text/css" href="presentation.css" />
    </head>
    <body>
        <h1>
            <img src="avgeek.png" alt="Aviation GeekS!" style="display: inline"
                width="200" height="150" /> Login Page
        </h1>
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
            <a href="signup1.html">Don't have an account? Sign-up here!</a>
        </p>
    </body>
</html>
