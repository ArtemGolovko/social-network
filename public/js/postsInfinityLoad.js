$(document).ready(function () {
    const mainContent = $('.mainContent');
    const loadPostUrl = mainContent.data('loadPostsUrl');
    let totalLoaded = 0;
    const maxResult = 2;
    $(window).scroll(function () {
        if ($(window).scrollTop() >= $(document).height() - $(window).height() - 25) {
            mainContent.append(loadPosts(loadPostUrl, totalLoaded, maxResult));
            totalLoaded += maxResult;
        }
    });

    while ($(window).scrollTop() >= $(document).height() - $(window).height() - 25) {
        $('.mainContent').append(loadPosts(loadPostUrl, totalLoaded, maxResult));
        totalLoaded += maxResult;
    }
});

function loadPosts(url, totalLoaded, maxResult)
{
    let html;
    $.ajax({
        url: url,
        async: false,
        type: 'POST',
        dataType: 'html',
        contentType: 'application/json;charset=utf-8',
        data: JSON.stringify({
            startIndex: totalLoaded,
            maxResult: maxResult
        })
    }).done(function (data) {
        html = data;
    });
    return html;
}