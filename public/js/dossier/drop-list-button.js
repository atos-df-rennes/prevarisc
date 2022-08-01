window.onload = function() {
    $('.deploy').click(function () {
        const btnIcon = $(this).children('i').first()

        if (btnIcon[0].className === 'icon-minus') {
            btnIcon.removeClass().addClass('icon-plus')
            $('#'+($(this).attr('type-dossier'))).hide()
        } else {
            btnIcon.removeClass().addClass('icon-minus')
            $('#'+($(this).attr('type-dossier'))).show()
        }
    });
};