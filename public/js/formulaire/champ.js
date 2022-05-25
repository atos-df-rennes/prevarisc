$(document).ready(function() {
    const typeChampSelect = $('#type_champ')

    typeChampSelect.on('change', function() {
        if (typeChampSelect.find(":selected").text() === 'Liste') {
            $('#div-list-value').show()
        } else {
            $('#div-list-value').hide()
        }
    })

    $('#add-list-value').on('click', function(e) {
        e.preventDefault()
        const date = Date.now()
        const html = "<div><input type='text' name='valeur-ajout-"+date+"' id='valeur-ajout-"+date+"'></input><a href='#' class='delete-list-value pull-right'>Retirer</a></div>"

        $('#valeurs-liste').append(html)

        $('#edit-rubrique .delete-list-value').on('click', function(e) {
            e.preventDefault()
            $(this).parent().remove()
        })
    })

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
})