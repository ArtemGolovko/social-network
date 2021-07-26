$(document).ready(function(){
    $('.postMore').click(function(event){
        $(this).children('.postMoreMenu').toggleClass("active");
    });

    $('.btn_like').click(function(event){
        let $this = $(this);

        $.ajax({
            url: $(this).data(`${$(this).data('action')}Url`),
            type: 'POST',
            dataType: 'json'
        }).done(function(data) {
            console.log('data');
            $this.parent().children('#likesCount').text(data.likesCount);
            $this.data('action', ($(this).data('action') === 'like') ? 'dislike' : 'like');

            $this.children('.fa-heart').toggleClass("active");
            $this.toggleClass("active");
            $this.children('.fa-heart').toggleClass("animate__bounceIn animate__flip animate__bounce");
        }).fail(function (data) {
            window.location.href = data.responseJSON.redirectUrl;
        });
    });

    $('.notificationIcon').click(function(event){
        $('.notificationList').toggleClass("active");
    });
    $('.userIcon').click(function(event){
        $('.profileMenuHeader').toggleClass("active");
    });
    $('.btn_comment').click(function(event){
        let postComments = $(this).parents().eq(2).children('.postComments');
        let $this = $(this);

        if ($(this).data('loaded') === false) {
            $.ajax({
                url: $(this).data('comments-url'),
                type: 'POST',
                dataType: 'json',
                contentType: 'application/json;charset=utf-8',
                data: JSON.stringify({
                    startIndex: 0,
                    maxResult: 2
                })
            }).done(function (data) {
                for (let comment of data.comments) {
                    postComments.children('#pasteComments').before(createComment(comment));
                }
                $this.data('loaded', true);
            });
        }
        postComments.toggleClass("active");
    });
    $('.subscribeProfile').click(function(event){
        $('.subscribeProfile').toggleClass("active");
        var change = document.getElementById("subscribeLabel");
            if (change.innerHTML == "ВЫ ПОДПИСАНЫ")
            {
                change.innerHTML = "ПОДПИСАТСЯ";
            }
            else {
                change.innerHTML = "ВЫ ПОДПИСАНЫ";
            }
    });
 });


function createComment(comment)
{
    return `<div class="postComment">
            <div class="postCommentUser">
                <div class="postCommentUserIcon" style="background-image: url(${comment.author.avatar}); background-size: cover;"></div>
                <div class="postUserInfo">
                    <div class="postCommentUsername">${comment.author.username} <i class="fas fa-check-circle verifyIcon"></i></div>
                    <div class="postCommentTime">${comment.createdAt}</div>
                </div>
            </div>
            <div class="postCommentContent">
                <p>${comment.body}</p>
            </div>
            <button class="postCommentReplyButton">Ответить</button>
        </div>`;
}