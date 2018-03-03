//run code when jquery is ready
jQuery(document).ready(function ($){
    var $postContent = $('[name=postContent]');
    var $characterCounter = $('.characterCounter');
    var $charactersRemaining = $('.remainingCharacters');
    $postContent.on('keyup', function (e){
        var $this = $(this);
        var charNumber = $this.val().trim().length;
//display how many characters
        $characterCounter.text(charNumber);
        $charactersRemaining.text(250-charNumber);
        if (charNumber > 250){
            $characterCounter.addClass('exceeded');
            $charactersRemaining.addClass('exceeded');
        } else {
            $characterCounter.removeClass('exceeded');
            $charactersRemaining.removeClass('exceeded');
        }
    });
});
