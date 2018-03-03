jQuery(document).ready(function ($){
    var $likeButton = $('.likebutton')
    var $dislikeButton = $('.dislikebutton')
    $likeButton.on('click', function(e){
        var $this = $(this);
        $this.toggleClass('clicked');
    });
    $dislikeButton.on('click', function(e){
        var $this = $(this);
        $this.toggleClass('clicked');
    });

});
