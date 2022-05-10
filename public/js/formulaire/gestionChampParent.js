$(document).ready(function() {

    let tableauParentChamp =  $('#tableParentChamp')

    let ajouterLigneTableau = () => {
        tableauParentChamp.append(
            `
            <tr id=`+$('#ID_CHAMP').val()+`>
                <td class='tdMove'><i class="icon-move"></i></td>
                <td>
                `
                +
                 document.getElementsByName('nom_champ')[1].value
                +
                `
                </td>
                <td>
                `
                +
                document.getElementsByName('type_champ')[1].options[document.getElementsByName('type_champ')[1].selectedIndex].text
                +
                `
                </td>
                <td id='actions'>`
                +
                `
                <div class='text-center'>
                    <a href='/formulaire/edit-champ/rubrique/8/champ/10'>
                        <i title='Modifier' class='icon-pencil'></i>
                    </a>
                    <a href='' data-id='/formulaire/edit-champ/rubrique/`+$('#ID_CHAMP').val()+`/champ/'`+$('#ID_CHAMP_PARENT').val()+` class='delete-champ' onclick='return deleteChamp(this)'>
                        <i title='Supprimer' class='icon-trash'></i>
                    </a>
                </div>
                `    
                +
                `
                </td>
            </tr>
            `

        )
    }


    $('#add-champ').on('click', function() {
        const form = this.closest('form')
        const idRubrique = $('#rubrique').val()
        const formData = $(form).serialize()
        $.ajax({
            url: '/formulaire/add-champ',
            data: formData+'&rubrique='+idRubrique,
            type: 'POST',
            success: function() {
                ajouterLigneTableau()
                form.reset()
            },
            error: function() {
                return false;
            }
        })
        return false
    })

 


})


