$(document).ready(function(){
    viewport_height = $(window).height();
    viewport_width = $(window).width();
    final_height = viewport_height - $('header').height() - $('footer').height() -50 ;

    $('#add-picture-field').click(function(){
        file_input = $('input[type="file"]:first').clone();
        $('#album-pictures').append(file_input)
                      .append($('<br />'));
        $(file_input).val('')
                     .focus();
        return false;
    });
    $("#cycle").cycle({
        fx:   'fade',
    });
    $('.text').lousyField()
    $('form').submit(function(){
        $(this).find('.text').noCrap()
    })
    $('#description .handle').click(function(){
        $('#description').toggleClass('collapsed');
        $('#content').toggle();
        return false;
    });
    $('.thumb a').click(function(){
        target = $(this).attr('href');
        $('#description:not(.collapsed)').toggleClass('collapsed');
        $('#content').hide();
        $.scrollTo(target, {duration:1000, axis:'y'});
        return false;
    });
    $('#display img').attr('height', final_height);
    album_list_height = ( final_height - $('#album-list').height()) / 2;  
    $('#album-list').css({'margin-top':album_list_height});
    $('#display .middle').height(final_height-50);
})
