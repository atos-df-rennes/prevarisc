window.onload = function(){
    $('.deploy').click(function (e) { 
        if($(this).children()[0].className == 'icon-minus'){
            $(this).children().first().removeClass().addClass('icon-plus')
            $('#'+($(this).attr('attr'))).hide()
        }else{
            $(this).children().first().removeClass().addClass('icon-minus')
            $('#'+($(this).attr('attr'))).show()
        }
    });
};