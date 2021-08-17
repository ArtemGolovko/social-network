$(document).ready(function(){
    let homepageLabel = $('#homepage');
    if (homepageLabel.children('a').attr('href') === window.location.pathname) {
        homepageLabel.children('i').attr('style', 'color: rgb(255, 136, 0);');
        homepageLabel.children('a').attr('style','color: rgb(255, 136, 0); font-weight: 700;');
    }

    $(document).on('click', '.postMore', function(event){
        $(this).children('.postMoreMenu').toggleClass("active");
    });

    $(document).on('click', '.btn_like',function(event){
        let $this = $(this);
        let $$this = this;

        $.ajax({
            url: this.dataset[`${this.dataset.action}Url`],
            type: 'POST',
            dataType: 'json'
        }).done(function(data) {
            $this.parent().children('#likesCount').text(data.likesCount);
            $$this.dataset.action =  ($$this.dataset.action === 'like') ? 'dislike' : 'like';

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
    $(document).on('click', '.btn_comment', function(event){
        let postComments = $(this).parents().eq(2).children('.postComments');
        let $this = $(this);

        let url = $(this).data('comments-url');
        let loadMakeCommentBlockUrl = $(this).data('loadMakeCommentBlockUrl');

        if ($(this).data('loaded') === false) {
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'html',
                contentType: 'application/json;charset=utf-8',
                data: JSON.stringify({
                    startIndex: 0,
                    maxResult: 2
                })
            }).done(function (data) {
                postComments.prepend(data);
                postComments.append(loadHtml(loadMakeCommentBlockUrl));
                $this.attr('data-loaded', true);
            });
        }
        postComments.toggleClass("active");
    });
    $(document).on('click', '.postMoreCemmentsBtn', function (event) {
        let $this = $(this);

        let url = $(this).data('commentsUrl');
        let totalLoaded = $(this).data('totalLoaded');

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'html',
            contentType: 'application/json;charset=utf-8',
            data: JSON.stringify({
                startIndex: totalLoaded,
                maxResult: 2
            })
        }).done(function (data) {

            $this.after(data);

            $this.remove();
        });
    });

    $(document).on('click', '.postMoreReply', function (event) {
        let $this = $(this);

        let url = $(this).data('replaysUrl');
        let totalLoaded = $(this).data('totalLoaded');

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'html',
            contentType: 'application/json;charset=utf-8',
            data: JSON.stringify({
                startIndex: totalLoaded,
                maxResult: 2
            })
        }).done(function (data) {
            $this.after(data);
            $this.remove();
        });
    });

    $('.publishButtonPost').click(function (event) {
        let csrfToken =  $(this).data('_csrf_token');
        let parent = $(this).parent();
        let postBody = parent.children('.mkpLeft').children('.mkpInput').val();

        $.ajax({
            url: $(this).data('createPostUrl'),
            type: 'POST',
            dataType: 'json',
            contentType: 'application/json;charset=utf-8',
            data: JSON.stringify({
                '_csrf_token': csrfToken,
                'postBody': postBody
            })
        }).done(function (data) {
            parent.children('.mkpLeft').children('.mkpInput').val('');
            parent.after(data.html);
        });
    });
    $(document).on('click', '.makeCommentBlock .publishButtonComment', function (event) {
        let csrf_token = $(this).data('_csrf_token');
        let input = $(this).parent().children('input');
        let postComments = $(this).parents().eq(2);

        $.ajax({
            url: $(this).data('createCommentUrl'),
            type: 'POST',
            dataType: 'json',
            contentType: 'application/json;charset=utf-8',
            data: JSON.stringify({
                '_csrf_token': csrf_token,
                'commentBody': input.val()
            })
        }).done(function (data) {
            input.val('');
            postComments.prepend(data.html);
        });
    });
    $(document).on('click', '.makeReplyBlock .publishButtonComment', function (event) {
        let csrf_token = $(this).data('_csrf_token');
        let input = $(this).parent().children('input');
        let commentReplays = $(this).parents().eq(2).children('.postCommentContent');

        $.ajax({
            url: $(this).data('createReplayUrl'),
            type: 'POST',
            dataType: 'json',
            contentType: 'application/json;charset=utf-8',
            data: JSON.stringify({
                '_csrf_token': csrf_token,
                'commentBody': input.val()
            })
        }).done(function (data) {
            input.val('');
            commentReplays.after(data.html);
        });
    });
    $(document).on('click', '.postCommentReplyButton', function (event) {
        $(this).parent().append(loadHtml($(this).data('load-make-replay-block-url')));
        $(this).remove();
    });
    $(document).on('click', '.btn_share', function (event) {
         let url = $(this).data('url');
         $.ajax({
             url: url,
             type: 'POST',
             dataType: 'json'
         }).done(function (data) {
             window.location.href = data.url;
         });
    });

    $('.mkpInput').click(function(event){
        $('.publishButtonPost').toggleClass("active");
        $('.postIconMkp').toggleClass("active");
    });

    $('.subscribeProfile').click(function(event){
        $('.subscribeProfile').toggleClass("active");
        var change = document.getElementById("subscribeLabel");
        if (change.innerHTML == "ПОДПИСКИ")
        {
            change.innerHTML = "ПОДПИСАТСЯ";
        }
        else {
            change.innerHTML = "ПОДПИСКИ";
        }
    });
});

function loadHtml(url)
{
    let html;
    $.ajax({
        url: url,
        type: 'POST',
        async: false,
        dataType: 'html'
    }).done(function (data) {
        html = data;
    });

    return html;
}
