function display_error(message, $element){
    var messageElem = '<span class="validation-message">' + message + '</span>';

    var $fieldRow = $element.closest("tr")

    $fieldRow.addClass("error");
    var $existingMessage = $fieldRow.find('.validation-message');
    if ($existingMessage.length) {
        $existingMessage.text(message);
    } else {
        $element.before(messageElem);
    }
}



function validate_email(email) {
    var email_regex = /\S+@\S+\.\S+/;
    return email_regex.test(email);
}

function validate_password(password) {
    var password_regex = /^[a-z]+$/i;
    if (password.length >= 8) {
        if (password_regex.test(password)==false){
            return true;
        }
    }
    return false;
}

function validate_dateofbirth(dateofbirth) {
    var dob_regex = /^\d{1,2}\/\d{1,2}\/\d{4}$/;
    if (dob_regex.test(dateofbirth)==true){
        return true;
    } else {
        return false;
    }
}

function validate_username(username) {
    var space_regex = /^\S*$/;
    var wordchar_regex = /^\w+$/;
    if (space_regex.test(username)==true){
        if(wordchar_regex.test(username)==true){
            return true;
        }
    }
    return false;
}
function validateLogInForm(){
    var submit = true;
    var $username = $('[name=uname]');
    var $password = $('[name=pswd]');

    if (validate_username($username.val()) == false){
        display_error("Username cannot contain spaces or special characters.", $username);
        submit = false;
    }
    if (validate_password($password.val()) == false){
        display_error("Password must be at least 8 characters long and have at least 1 number or symbol.", $password);
        submit = false;
    }
    return submit;
}

function validateSignUpForm(){


    var submit = true;
    var $username = $('[name=uname]');
    var $password = $('[name=pswd]');
    var $confirm_pswd = $('[name=confirm_pswd]');
    var $dob = $('[name=dob]');
    var $email = $('[name=email]');

    if (validate_username($username.val()) == false){
        display_error("Username cannot contain spaces or special characters.", $username);
        submit = false;
    }
    if (validate_password($password.val()) == false){
        display_error("Password must be at least 8 characters long and have at least 1 number or symbol.", $password);
        submit = false;
    }
    if ($password.val() != $confirm_pswd.val()){
        display_error("Passwords must match", $confirm_pswd);
        submit = false;
    }
    if (validate_dateofbirth($dob.val()) == false){
        display_error("Date of birth must follow DD/MM/YYYY.", $dob);
        submit = false;
    }
    if (validate_email($email.val()) == false){
        display_error("Email provided is invalid.", $email);
        submit = false;
    }
    return submit;
}
