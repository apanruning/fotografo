$(document).ready(function(){
    function fadeHeader(){
        $('.fade').fadeOut('slow');
    };
    window.setTimeout(fadeHeader, 1000)
    $('.fade-wrapper').hover(function(){
       $('.fade').fadeIn();
    }, function(){
       $('.fade').fadeOut('slow');
    })
})
