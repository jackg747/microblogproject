jQuery(document).ready(function ($){
    var $likeButton = $('.likebutton');
    var $dislikeButton = $('.dislikebutton');
    var $dislikeCounter = $('.reaction-counter', $dislikeButton);

    $likeButton.on('click', function(e){
        var $this = $(this);
        $this.toggleClass('clicked');
        $this.parent().find('.dislikebutton').removeClass('clicked');
        $.ajax('react.php', {
            counters: {
                like: $this.find('.reaction-counter'),
                dislike: $this.parent().find('.dislikebutton').find('.reaction-counter')
            },
            method: 'POST',
            data: {
                action: 'like',
                postId: $this.attr('data-post-id')
            },
            success: function(data) {
                var data = JSON.parse(data);
                this.counters.like.text(data["1"]);
                this.counters.dislike.text(data["-1"]);
            }
        });
    });
    $dislikeButton.on('click', function(e){
        var $this = $(this);
        $this.toggleClass('clicked');
        $this.parent().find('.likebutton').removeClass('clicked');
        $.ajax('react.php', {
            counters: {
                like: $this.parent().find('.likebutton').find('.reaction-counter'),
                dislike: $this.find('.reaction-counter')
            },
            method: 'POST',
            data: {
                action: 'dislike',
                postId: $this.attr('data-post-id')
            },
            success: function(data) {
                var data = JSON.parse(data);
                this.counters.like.text(data["1"]);
                this.counters.dislike.text(data["-1"]);
            }
        });
    });
});
