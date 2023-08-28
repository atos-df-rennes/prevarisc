$(document).ready(function() {
    const selectType = $('select[name="TYPE"]')
    const submit = $('input[type="submit"]')

    selectType.on('change', function() {
        const val = selectType.val()

        if (val === 'Avis') {
            submit.val("Ajouter l'avis")
        } else {
            submit.val("Ajouter la dérogation")
        }
    })

    // Gestion du bouton de désélection
    const deselectButton = document.getElementById('deselect')

    if (deselectButton) {
        deselectButton.addEventListener('click', function() {
            const idDossier = $('.avis-derogation__leve input:checked')[0].id

            document.getElementById(idDossier).checked = false
            
            $('#deselect').hide()
        })
    }

    $('.avis-derogation__leve input').on('change', function() {
        $('#deselect').show()
    })
})