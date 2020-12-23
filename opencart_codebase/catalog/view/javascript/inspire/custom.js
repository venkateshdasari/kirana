
 /* loader */
$(document).ready(function(){
    var o = $('#page-preloader');
    if (o.length > 0) {
        $(window).on('load', function() {
            $('#page-preloader').removeClass('visible');
        });
    }
});

//go to top
$(document).ready(function () {
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('#scroll').fadeIn();
        } else {
            $('#scroll').fadeOut();
        }
    });
    $('#scroll').click(function () {
        $("html, body").animate({scrollTop: 0}, 600);
        return false;
    });

    $('.rightbanner').appendTo('.sliderightbanner');
});


/* responsive menu */
 function openNav() {
    $('body').addClass("active");
    document.getElementById("mySidenav").style.width = "250px";
    jquery('#mySidenav').addCss("display","block");
}
function closeNav() {
    $('body').removeClass("active");
    document.getElementById("mySidenav").style.width = "0";
    jquery('#mySidenav').removeCss("display","none");
}


/* pageloader */
$(window).load(function() {
  $("#loading").delay(500).fadeOut(500);
  $("#loading-center").click(function() {
  $("#loading").fadeOut(500);
  })
})

$(window).scroll(function(){
    if ($(window).scrollTop() >= 300) {
       $('.menufull').addClass('fixed-header');
    }
    else {
       $('.menufull').removeClass('fixed-header');
    }
});


$(document).ready(function(){
    if ($(window).width() <= 991){
    var count_block = $('.moremenu').length;
    var number_blocks = 8;
    //console.log(count_block);
    if(count_block < number_blocks){
        return false; 
    }
    else {
        $('.moremenu').each(function(i,n){
            $('moremenu').addClass(i);
            if(i == number_blocks) {
            $(this).append('<li class="view_more my-menu"><i class="fa fa-plus thumb_img"></i> <a class="dropdown-toggle">More Categories</a></li>');
            }
            if(i> number_blocks) {
            $(this).addClass('wr_hide_menu').hide();
            }
        });
        $('.view_more').click(function() {
            $(this).toggleClass('active');
            $('.wr_hide_menu').slideToggle();
        });
    }
}
else{
    var count_block = $('.moremenu').length;
    var number_blocks = 9;
    //console.log(count_block);
    if(count_block < number_blocks){
        return false; 
    }
    else {
        $('.moremenu').each(function(i,n){
            $('moremenu').addClass(i);
            if(i == number_blocks) {
            $(this).append('<li class="view_more my-menu"><i class="fa fa-plus thumb_img"></i> <a class="dropdown-toggle">More Categories</a></li>');
            }
            if(i> number_blocks) {
            $(this).addClass('wr_hide_menu').hide();
            }
        });
        $('.view_more').click(function() {
            $(this).toggleClass('active');
            $('.wr_hide_menu').slideToggle();
        });
    }
}
});




