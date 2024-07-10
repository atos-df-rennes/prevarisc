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
        const savedFieldsDiv = $('#saved-fields')
        const idRubrique = $('#rubrique-id').val()
        const savedFieldsTitlesDiv = $('.titles')

        const currentUrl = window.location.href
        const isParent = currentUrl.includes('edit-champ')

        const formData = $(form).serialize()
        $.ajax({
            url: '/formulaire/add-champ?isParent='+isParent,
            data: formData+'&rubrique='+idRubrique,
            type: 'POST',
            success: function(data) {
                const jsonParsedData = JSON.parse(data)
                const parsedData = Array.isArray(jsonParsedData) ? jsonParsedData[0] : jsonParsedData

                // On crée la table uniquement si elle n'existe pas
                if (savedFieldsDiv.children().length === 0) {
                    let titleText = 'Liste des champs'
                    if (isParent) {
                        titleText += ' enfants'
                    }

                    savedFieldsTitlesDiv.append(`<div class="span6 offset2">
                        <h3>${titleText}</h3>
                    </div>`)
                    savedFieldsDiv.append(getTableElement())
                }
                
                const table = savedFieldsDiv.children('table')
                table.append(getRowElement(parsedData))

                if (parsedData.TYPE === 'Liste') {
                    const typeRow = table.children('tbody').children().children('#type-'+parsedData.ID_CHAMP)

                    if (parsedData.VALEUR !== null) {
                        typeRow.append(getListElements(jsonParsedData))
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
        const html = "<div><input type='text' name='valeur-ajout-"+date+"' id='valeur-ajout-"+date+"'></input><a href='#' class='delete-list-value pull-right'>Retirer</a></div>"

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
    const idRubrique = element.getAttribute('data-rubrique-id')
    const idParent = element.getAttribute('data-id-parent')
    const nom = element.getAttribute('data-nom')

    $('#dialog-supp').dialog("destroy")
    $("#dialog-supp").remove()

    const dialog_supp = $("<div id='dialog-supp'></div>").appendTo("body")
    dialog_supp.html('Vous êtes sur le point de supprimer le champ "' + nom + '".')
    dialog_supp.dialog({
        title: "Suppression d'un champ",
        width: 650,
        draggable: false,
        resizable: false,
        modal: true,
        buttons: [
            {
                text: 'Supprimer',
                class: 'btn btn-danger',
                click: function() {
                    const parentDiv = $(element).parent().parent().parent()
                    const parentTable = $(element).closest('table')
                    const nbOfRows = parentTable.children('tbody').children('tr').length

                    const parentDivTitleDiv = $('.titles .span6.offset2')

                    $.ajax({
                        url: '/formulaire/delete-champ/rubrique/'+idRubrique+'/champ/'+idChamp,
                        type: 'POST',
                        data: idParent,
                        success: function() {
                            if (nbOfRows === 1) {
                                parentTable.remove()
                                parentDivTitleDiv.remove()
                            } else {
                                parentDiv.remove()
                            }
                        },
                        error: function() {
                            return false
                        }
                    })

                    dialog_supp.dialog("close")
                    $("#dialog-supp").html('')

                    return false
                }
            },
            {
                text: 'Annuler',
                class: 'btn',
                click: function() {
                    dialog_supp.dialog("close")
                    $("#dialog-supp").html('')
                }
            }
        ]
    })
}

function getTableElement() {
    return `<table class="table table-bordered table-condensed">
        <thead>
            <tr>
                <th></th>
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
    return `<tr>
        <td class="tdMove"><span class="glyphicon glyphicon-move" aria-hidden="true"></span></td>
        <td>`
        +parsedData.NOM+
        `</td>
        <td id='type-`+parsedData.ID_CHAMP+`'>`
        +parsedData.TYPE+
        `</td>
        <td id='actions'>
            <div class='text-center'>
                <a href='/formulaire/edit-champ/rubrique/`+parsedData.ID_RUBRIQUE+`/champ/`+parsedData.ID_CHAMP+`'>
                    <span title='Modifier' class='glyphicon glyphicon-pencil' aria-hidden='true'></span>
                </a>
                <button data-id='`+parsedData.ID_CHAMP+`' data-rubrique-id='`+parsedData.ID_RUBRIQUE+`' data-nom='`+parsedData.NOM+`' class='btn btn-link delete-champ' onclick='return deleteChamp(this)'>
                    <i title='Supprimer' class='glyphicon glyphicon-trash' aria-hidden='true'></i>
                </button>
            </div>
        </td>
    </tr>`
}

function getListElements(jsonParsedData) {
    let list = '<ul>'

    for (let i = 0; i < jsonParsedData.length; i++) {
        list += '<li>'+jsonParsedData[i].VALEUR+'</li>'
    }

    list += '</ul>'

    return list
}