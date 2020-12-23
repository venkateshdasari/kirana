//SB Theme Go To Top 
$(document).ready(function () {
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('#go-top').fadeIn();
        } else {
            $('#go-top').fadeOut();
        }
    });
    $('#go-top').click(function () {
        $("html, body").animate({scrollTop: 0}, 900);
        return false;
    });
});