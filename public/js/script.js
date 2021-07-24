$(document).ready(function(){
    $('.postMore').click(function(event){
        $(this).children('.postMoreMenu').toggleClass("active");
    });

    $('.btn_like').click(function(event){
        let $this = $(this);

        $.ajax({
            url: $(this).data(`${$(this).data('action')}Url`),
            type: 'POST',
            dataType: 'json',
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