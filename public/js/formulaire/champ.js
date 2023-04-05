$(document).ready(function() {
    $('#edit-champ .delete-list-value').on('click', function() {
        const id = this.getAttribute('data-id')
        const parentDiv = $(this).parent()

        $.ajax({
            url: '/formulaire/delete-valeur-liste/liste/'+id,
            type: 'POST',
            success: function() {
                parentDiv.remove()
            },
            error: function() {
                return false
            }
        })
        return false
    })

    const typeChampSelect = $('#type_champ_enfant')
    
    typeChampSelect.on('change', function() {
        if (typeChampSelect.find(":selected").text() === 'Liste') {
            $('#div-list-value').show()
        } else {
            $('#div-list-value').hide()
        }
    })
})