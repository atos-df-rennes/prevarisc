window.onload = function() {
    $('.deploy').click(function () {
        const btnIcon = $(this)
        if (btnIcon[0].innerText === 'Voir moins') {
            btnIcon[0].innerText = 'Voir plus'
            $('#'+($(this).attr('type-dossier'))).hide()
        } else {
            btnIcon[0].innerText = 'Voir moins'
            $('#'+($(this).attr('type-dossier'))).show()
        }
    });
};