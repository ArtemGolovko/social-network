$(document).ready(function () {
    const mainContent = $('.mainContent');
    const loadPostUrl = mainContent.data('loadPostsUrl');
    let totalLoaded = 0;
    const maxResult = 2;
    $(window).scroll(function () {
        if ($(window).scrollTop() >= $(document).height() - $(window).height() - 25) {
            let posts = loadPosts(loadPostUrl, totalLoaded, maxResult);
            for (let post of posts) {
                $('.mainContent').append(post);
                ++totalLoaded;
            }

            if (posts.length < maxResult) {
                $(window).off('scroll');
            }
        }
    });

    while ($(window).scrollTop() >= $(document).height() - $(window).height() - 25) {
        let posts = loadPosts(loadPostUrl, totalLoaded, maxResult);
        for (let post of posts) {
            $('.mainContent').append(post);
            ++totalLoaded;
        }

        if (posts.length < maxResult) {
            break;
        }
    }
});

function loadPosts(url, totalLoaded, maxResult)
{
    let posts;
    $.ajax({
        url: url,
        async: false,
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json;charset=utf-8',
        data: JSON.stringify({
            startIndex: totalLoaded,
            maxResult: maxResult
        })
    }).done(function (data) {
        posts = data;
    });
    return posts;
}