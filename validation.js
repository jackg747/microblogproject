function display_error(message, $element){
    var messageElem = '<span class="validation-message">' + message + '</span>';
//finds closest fieldparent class
    var $fieldRow = $element.closest(".fieldparent")
    //adds error class for css file
    $fieldRow.addClass("error");
    //check for validation message
    var $existingMessage = $fieldRow.find('.validation-message');
//if existing message, update message provided
    if ($existingMessage.length) {
        $existingMessage.text(message);
        //if there isnt one, insert a new one
    } else {
        $element.before(messageElem);
    }
}
function hide_error($element){
    var $fieldRow = $element.closest(".fieldparent")
    //removes error class for css file
    $fieldRow.removeClass("error");
    //check for validation message
    var $existingMessage = $fieldRow.find('.validation-message');
//if existing message, update message provided
    if ($existingMessage.length) {
        $existingMessage.remove();
        //if there isnt one, insert a new one
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
    var $email = $('[name=email]');
    var $password = $('[name=pswd]');

    if (validate_email($email.val()) == false){
        display_error("Email provided is invalid.", $email);
        submit = false;
    } else{
        hide_error($email);
    }
    if (validate_password($password.val()) == false){
        display_error("Password must be at least 8 characters long and have at least 1 number or symbol.", $password);
        submit = false;
    }else{
        hide_error($password);
    }
    return submit;
}

function validateSignUpForm(){
    var submit = true;
    var $username = $('[name=username]');
    var $password = $('[name=password]');
    var $confirm_pswd = $('[name=confirm_password]');
    var $dob = $('[name=date_of_birth]');
    var $email = $('[name=email]');

    if (validate_username($username.val()) == false){
        display_error("Username cannot contain spaces or special characters.", $username);
        submit = false;
    } else{
        hide_error($username);
    }
    if (validate_password($password.val()) == false){
        display_error("Password must be at least 8 characters long and have at least 1 number or symbol.", $password);
        submit = false;
    } else{
        hide_error($password);
    }

    if ($password.val() != $confirm_pswd.val()){
        display_error("Passwords must match", $confirm_pswd);
        submit = false;
    } else{
        hide_error($confirm_pswd);
    }
    if (validate_dateofbirth($dob.val()) == false){
        display_error("Date of birth must follow DD/MM/YYYY.", $dob);
        submit = false;
    } else{
        hide_error($dob);
    }
    if (validate_email($email.val()) == false){
        display_error("Email provided is invalid.", $email);
        submit = false;
    } else{
        hide_error($email);
    }
    return submit;
}
function validatePostForm(){
    var submit = true;
    var $postContent = $('[name=postContent]');
    //store trimmed value of postContent
    var contentText = $postContent.val().trim();


    if (contentText.length == 0){
        display_error("Your post must not be empty.", $postContent)
        submit = false;
    } else if (contentText.length > 250) {
        display_error("Your post must not exceed 250 characters.", $postContent)
        submit = false;
    } else {
        hide_error($postContent);
    }
    return submit;
}
