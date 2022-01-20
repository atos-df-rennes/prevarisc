$(document).ready(function() {
    const typeChampSelect = $('#type_champ')

    typeChampSelect.on('change', function() {
        if (typeChampSelect.find(":selected").text() === 'Liste') {
            $('#div-list-value').show()
        } else {
            $('#div-list-value').hide()
        }
    })

    $('#add-champ').on('click', function() {
        const form = this.closest('form')
        const formData = $(form).serialize()

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

    $('.delete-champ').on('click', function() {
        const idChamp = this.getAttribute('data-id')
        const idRubrique = this.getAttribute('data-id')

        const parentDiv = $(this).parent().parent()
        const parentTable = $(this).closest('table')
        const nbOfRows = parentTable.children('tbody').children('tr').length

        const parentTableDiv = $(this).closest('.row-fluid')
        const parentDivTitlesDiv = parentTableDiv.prev().children()
        let parentDivTitleDiv = null

        for (let i = 0; i < parentDivTitlesDiv.length; i++) {
            if (parentDivTitlesDiv[i].className === 'span7 offset1') {
                parentDivTitleDiv = parentDivTitlesDiv[i]
            }
        }

        $.ajax({
            url: '/formulaire/delete-champ/rubrique/'+idRubrique+'/champ/'+idChamp,
            type: 'POST',
            success: function() {
                if (nbOfRows === 1) {
                    parentTable.remove()
                    if (parentDivTitleDiv !== null) {
                        parentDivTitleDiv.remove()
                    }
                } else {
                    parentDiv.remove()
                }
            },
            error: function() {
                return false
            }
        })
        return false
    })

    $('#add-list-value').on('click', function(e) {
        e.preventDefault()
        const date = Date.now()
        const html = "<div><input type='text' name='valeur-"+date+"' id='valeur-"+date+"'></input><a href='#' class='delete-list-value pull-right'>Retirer</a></div>"

        $('#list-value').append(html)

        $('.delete-list-value').on('click', function(e) {
            e.preventDefault()
            parentDiv = $(this).parent()
            parentDiv.remove()
        })
    })
})