$(document).ready(function(){
    let homepageLabel = $('#homepage');
    if (homepageLabel.children('a').attr('href') === window.location.pathname) {
        homepageLabel.children('i').attr('style', 'color: rgb(255, 136, 0);');
        homepageLabel.children('a').attr('style','color: rgb(255, 136, 0); font-weight: 700;');
    }

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

        let url = $(this).data('comments-url');

        if ($(this).data('loaded') === false) {
            $.ajax({
                url: url,
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
                if (data.isMoreAvailable === true) {
                    postComments.children('#pasteComments').before(createViewMoreCommentsButton(2, url));
                }
                $this.data('loaded', true);
            });
        }
        postComments.toggleClass("active");
    });
    $(document).on('click', '.postMoreCemmentsBtn', function (event) {
        let postComments = $(this).parents();
        let $this = $(this);

        let url = $(this).data('commentsUrl');
        let totalLoaded = $(this).data('totalLoaded');

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            contentType: 'application/json;charset=utf-8',
            data: JSON.stringify({
                startIndex: totalLoaded,
                maxResult: 2
            })
        }).done(function (data) {

            $this.remove();

            for (let comment of data.comments) {
                postComments.children('#pasteComments').before(createComment(comment));
            }
            if (data.isMoreAvailable === true) {
                postComments.children('#pasteComments').before(createViewMoreCommentsButton(totalLoaded + 2, url));
            }
        });
    });

    $(document).on('click', '.postMoreReply', function (event) {
        let postComment = $(this).parents();
        let $this = $(this);

        let url = $(this).data('answersUrl');
        let totalLoaded = $(this).data('totalLoaded');

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            contentType: 'application/json;charset=utf-8',
            data: JSON.stringify({
                startIndex: totalLoaded,
                maxResult: 2
            })
        }).done(function (data) {
            $this.remove();

            for (let comment of data.answers) {
                postComment.children('#pasteAnswers').before(createComment(comment));
            }
            if (data.isMoreAvailable === true) {
                postComment.children('#pasteAnswers').before(createViewMoreCommentsButton(totalLoaded + 2, url));
            }
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


function createComment(comment) {
    let commentHtml = `<div class="postComment">
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
            <div style="display: none" id="pasteAnswers"></div>`;

    if (comment.hasAnswers) {
        commentHtml += createViewMoreAnswersMutton(0, comment.answersUrl);
    }


    commentHtml += `</div>`;
    return commentHtml;
}

function createAnswer(answer) {
    let answerHtml =`<div className="postCommentReply">
        <div className="postCommentUser">
            <div className="postCommentUserIconReply"
                 style="background-image: url(${answer.author.avatar}); background-size: cover;"></div>
            <div className="postUserInfo">
                <div className="postCommentUsername">${answer.author.username} <i className="fas fa-check-circle verifyIcon"
                                                                            aria-hidden="true"></i></div>
                <div className="postCommentTime">${answer.createdAt}</div>
            </div>
        </div>
        <div className="postCommentContent">
            <p>${answer.body}</p>
        </div>
        <button className="postCommentReplyButton">Ответить</button>
        <div style="display: none" id="pasteAnswers"></div>`;

    if (answer.hasAnswers) {
        answerHtml += createViewMoreAnswersMutton(0, answer.answersUrl);
    }

    answerHtml += `</div>`;

    return answerHtml;
}

function createViewMoreCommentsButton(totalLoaded, commentsUrl) {
    return `<button
                class="postMoreCemmentsBtn"
                data-total-loaded="${totalLoaded}"
                data-comments-url="${commentsUrl}"
            >Показать больше комментариев <i class="fas fa-chevron-down" aria-hidden="true"></i></button>`;
}

function createViewMoreAnswersMutton(totalLoaded, answersUrl) {
    return `<button
                class="postMoreReply"
                data-total-loaded="${totalLoaded}"
                data-answers-url="${answersUrl}"
            >Еще <i class="fas fa-chevron-down" aria-hidden="true"></i></button>`
}