import '../css/app.css';

import $ from 'jquery';

$("window").on('load', function() {
    $("body").removeAttr("id");
});

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
        $.ajax({
            url: this.dataset[`${this.dataset.action}Url`],
            type: 'POST',
            dataType: 'json'
        }).done(data => {
            $(this).parent().children('#likesCount').text(data.likesCount);
            this.dataset.action =  (this.dataset.action === 'like') ? 'dislike' : 'like';

            $(this).children('.fa-heart').toggleClass("active");
            $(this).toggleClass("active");
            $(this).children('.fa-heart').toggleClass("animate__bounceIn animate__flip animate__bounce");
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

        let url = $(this).data('comments-url');
        let loadMakeCommentBlockUrl = $(this).data('loadMakeCommentBlockUrl');

        if (this.dataset.loaded === 'false') {
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'html',
                contentType: 'application/json;charset=utf-8',
                data: JSON.stringify({
                    startIndex: 0,
                    maxResult: 2
                })
            }).done(data => {
                postComments.prepend(data);
                postComments.append(loadHtml(loadMakeCommentBlockUrl));
                $(this).attr('data-loaded', true);
            });
        }
        postComments.toggleClass("active");
    });
    $(document).on('click', '.postMoreCemmentsBtn', function (event) {
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
        }).done(data => {

            $(this).after(data);

            $(this).remove();
        });
    });

    $(document).on('click', '.postMoreReply', function (event) {
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
        }).done(data => {
            $(this).after(data);
            $(this).remove();
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
        }).done(data => {
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
        }).done(data => {
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
        }).done(data => {
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
        }).done(data => {
            window.location.href = data.url;
        });
    });

    $('.mkpInput').click(function(event){
        $('.publishButtonPost').toggleClass("active");
        $('.postIconMkp').toggleClass("active");
    });

    $('.subscribeProfile').click(function(event){

        $.ajax({
            url: $(this).data(this.dataset.action + 'Url'),
            type: 'POST',
        }).done(() => {
            $('.subscribeProfile').toggleClass("active");
            this.dataset.action =  (this.dataset.action === 'subscribe') ? 'unsubscribe' : 'subscribe';
            let change = document.getElementById("subscribeLabel");
            change.innerText = $(this).data(this.dataset.action + 'Message');
        });
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
    }).done(data => {
        html = data;
    });

    return html;
}