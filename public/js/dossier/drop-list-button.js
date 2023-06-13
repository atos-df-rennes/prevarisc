window.onload = function() {
    $('.deploy').click(function (event) {
        const clickedButton = event.target

        if (clickedButton.innerText === 'Voir moins') {
            clickedButton.innerText = 'Voir plus'
            $('#' + $(this).attr('type-dossier')).hide()
        } else {
            clickedButton.innerText = 'Voir moins'
            $('#' + $(this).attr('type-dossier')).show()
        }
    });
};