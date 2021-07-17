$(document).ready(function(){
    $('.postMore').click(function(event){
        $(this).children('.postMoreMenu').toggleClass("active");
    });

    $('.btn_like').click(function(event){
        $(this).children('.fa-heart').toggleClass("active");
        $(this).toggleClass("active");
        $(this).children('.fa-heart').toggleClass("animate__bounceIn animate__flip animate__bounce");
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