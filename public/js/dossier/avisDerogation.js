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
})