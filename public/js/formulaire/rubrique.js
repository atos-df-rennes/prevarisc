$(document).ready(function() {
    $('#field-form button').on('click', function() {
        const formData = $('#field-form').serialize()

        $.ajax({
            url: window.location.href,
            data: formData,
            type: 'POST',
            success: function() {
                location.reload()
            },
            error: function() {
                return false;
            }
        })
        return false
    })

    const typeChampSelect = $('#type_champ')

    typeChampSelect.on('change', function() {
        if (typeChampSelect.find(":selected").text() === 'Liste') {
            $('#div-valeurs-liste').show()
        } else {
            $('#div-valeurs-liste').hide()
        }
    })

    $('#add-list-value').on('click', function(e) {
        e.preventDefault()
        const date = Date.now()
        const html = "<div><input type='text' name='valeur-"+date+"' id='valeur-"+date+"'></input><a href='#' class='delete-list-value pull-right'>Retirer</a></div>"

        $('#valeurs-liste').append(html)

        $('.delete-list-value').on('click', function(e) {
            e.preventDefault()
            parentDiv = $(this).parent()
            parentDiv.remove()
        })
    })
})