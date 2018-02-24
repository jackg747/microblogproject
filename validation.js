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
        console.log("space regex passed")
        if(wordchar_regex.test(username)==true){
            return true;
        }
    }
    return false;
}
