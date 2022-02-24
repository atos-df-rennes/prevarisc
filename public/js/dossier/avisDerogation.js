$(document).ready(function() {
    const selectType = $('select[name="TYPE_AVIS_DEROGATIONS"]')
    const submit = $('input[type="submit"]')

    selectType.on('change', function() {
        const val = selectType.val()

        if (parseInt(val) === 1) {
            submit.val("Ajouter l'avis")
        } else {
            submit.val("Ajouter la dérogation")
        }
    })
})