jQuery(document).ready(function ($){
    setInterval(checkForNewReactions, 2000);
    setInterval(checkForNewPosts, 2000);
});

function getPostIds()
{
    var postIds = [],
        $postContainers = $('[data-post-id]');

    $postContainers.each(function() {
        postIds[$(this).attr('data-post-id')] = true;
    });

    return postIds;
}

function updatePostReactions(postId, reactionCounts) {
    $('[data-post-id=' + postId + ']').each(function () {
        var $this = $(this);

        $this.find('.likebutton .reaction-counter').text(reactionCounts.likes);
        $this.find('.dislikebutton .reaction-counter').text(reactionCounts.dislikes);
    });
}

var checkForNewPosts = function() {
    var postIds = getPostIds();

    $.ajax('liveupdate.php', {
        postIds: postIds,
        method: 'POST',
        data: {
            action: 'getNewPosts',
            source: $('.posts_container').attr('data-source'),
            userId: $('.posts_container').attr('data-user-id'),
            postIds: postIds
        },
        success: function(data, textStatus, xhr) {
            if (xhr.status == 204) {
                // console.log(data);
            } else {
                $('.posts_container').prepend(data);
            }
        }
    });
}

var checkForNewReactions = function() {
    var postIds = getPostIds();

    $.ajax('liveupdate.php', {
        postIds: postIds,
        method: 'POST',
        data: {
            action: 'getPostReactionCounts',
            postIds: postIds
        },
        success: function(data) {
            var data = JSON.parse(data);
            var postIds = Object.keys(data);

            for (var i = 0, len = postIds.length; i < len; i++) {
                updatePostReactions(postIds[i], data[postIds[i]])
            }
        }
    });
}
