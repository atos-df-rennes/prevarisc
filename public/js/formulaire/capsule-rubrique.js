$(document).ready(function() {
    $('.add-rubrique').on('click', function() {
        const form = this.closest('form')
        const capsuleRubrique = form.closest('div').id
        const savedRubriquesDiv = $('#saved-rubriques-'+capsuleRubrique)

        // Pour le controller
        const formData = $(form).serialize()
        // Pour l'affichage via Ajax
        const formDataArray = $(form).serializeArray()

        $.ajax({
            url: '/formulaire/add-rubrique',
            data: formData+'&capsule_rubrique='+capsuleRubrique,
            type: 'POST',
            success: function(id) {
                // On crée la table uniquement si elle n'existe pas
                if (savedRubriquesDiv.children().length === 0) {
                    savedRubriquesDiv.append(getTableElement())
                }

                const table = savedRubriquesDiv.children('table')
                table.append(getRowElement(formDataArray, id))

                form.reset()
            },
            error: function() {
                return false
            }
        })
        return false
    })

    $('.delete-rubrique').on('click', function(e) {
        e.preventDefault()
        deleteRubrique(this)
    })
})

function deleteRubrique(element) {
    const id = element.getAttribute('data-id')

    const parentDiv = $(element).parent().parent()
    const parentTable = $(element).closest('table')
    const nbOfRows = parentTable.children('tbody').children('tr').length

    const parentTableDiv = $(element).closest('.row-fluid')
    const parentDivTitlesDiv = parentTableDiv.prev().children()
    let parentDivTitleDiv = null

    for (let i = 0; i < parentDivTitlesDiv.length; i++) {
        if (parentDivTitlesDiv[i].className === 'span7 offset1') {
            parentDivTitleDiv = parentDivTitlesDiv[i]
        }
    }

    $.ajax({
        url: '/formulaire/delete-rubrique/rubrique/'+id,
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
}

function getTableElement() {
    return `<table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th>Nom de la rubrique</th>
                <th>Afficher la rubrique par défaut</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>`
}

function getRowElement(data, id) {
    const afficherRubrique = data.length === 3 ? data[2].value : data[1].value
    let defaultDisplay = ``;
    if (parseInt(afficherRubrique) === 1) {
        defaultDisplay =
            `<p class='text-center'>
                <i class='icon-ok'></i>
            </p>`
    }

    return `<tr>
        <td>`
        +data[0].value+
        `</td>
        <td>`
        +defaultDisplay+
        `</td>
        <td id='actions'>
            <a href='/formulaire/edit-rubrique/rubrique/`+id+`'>Modifier</a>
            <a href='#' data-id='`+id+`' class='delete-rubrique' onclick='return deleteRubrique(this)'>Supprimer</a>
        </td>
    </tr>`
}