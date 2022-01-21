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
        const savedRubriquesDiv = $('#saved-fields')
        const idRubrique = $('#rubrique-id').val()

        const formData = $(form).serialize()

        $.ajax({
            url: '/formulaire/add-champ',
            data: formData+'&rubrique='+idRubrique,
            type: 'POST',
            success: function(data) {
                // On crée la table uniquement si elle n'existe pas
                if (savedRubriquesDiv.children().length === 0) {
                    savedRubriquesDiv.append(getTableElement())
                }

                const parsedData = JSON.parse(data)
                const table = savedRubriquesDiv.children('table')
                table.append(getRowElement(parsedData))

                if (parsedData[0].TYPE === 'Liste') {
                    const typeRow = table.children('tbody').children().children('#type-'+parsedData[0].ID_CHAMP)

                    if (parsedData[0].VALEUR !== null) {
                        typeRow.append(getListElements(parsedData))
                    } else {
                        typeRow.append(`<div class="alert">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <strong>Attention&nbsp;!</strong>&nbsp;La liste ne possède aucune valeur, vous devriez en ajouter.
                        </div>`)
                    }
                }

                form.reset()
                $('#div-list-value').hide()
            },
            error: function() {
                return false;
            }
        })
        return false
    })

    $('.delete-champ').on('click', function(e) {
        e.preventDefault()
        deleteChamp(this)
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

function deleteChamp(element) {
    const idChamp = element.getAttribute('data-id')
    const idRubrique = element.getAttribute('data-id')

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
}

function getTableElement() {
    return `<table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th>Nom du champ</th>
                <th>Type du champ</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>`
}

function getRowElement(parsedData) {
    console.log(parsedData)

    return `<tr>
        <td>`
        +parsedData[0].NOM+
        `</td>
        <td id='type-`+parsedData[0].ID_CHAMP+`'>`
        +parsedData[0].TYPE+
        `</td>
        <td id='actions'>
            <a href='/formulaire/edit-champ/rubrique/`+parsedData[0].ID_RUBRIQUE+`/champ/`+parsedData[0].ID_CHAMP+`'>Modifier</a>
            <a href='#' data-id='`+parsedData[0].ID_CHAMP+`' data-rubrique-id='`+parsedData[0].ID_RUBRIQUE+`' class='delete-champ' onclick='return deleteChamp(this)'>Supprimer</a>
        </td>
    </tr>`
}

function getListElements(parsedData) {
    let list = '<ul>'

    for (let i = 0; i < parsedData.length; i++) {
        list += '<li>'+parsedData[i].VALEUR+'</li>'
    }

    list += '</ul>'

    return list
}