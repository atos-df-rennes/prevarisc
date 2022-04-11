$(document).ready(function() {

    const tableauParentChamp =  $('#tableParentChamp')

    const ajouterLigneTableau = (objectData = {}) => {

        tableauParentChamp.append(
            `
            <tr>
                <td>
                    1
                </td>
                <td>
                    2
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
        const idRubrique = $('#ID_CHAMP_PARENT').val()
        const formData = $(form).serialize()
        $.ajax({
            url: '/formulaire/add-champ',
            data: formData+'&rubrique='+idRubrique,
            type: 'POST',
            success: function(data) {
                const parsedData = JSON.parse(data)
                console.log("Pardsed data : ",parsedData)
                form.reset()
                ajouterLigneTableau()
            },
            error: function() {
                return false;
            }
        })
        return false
    })

 


})


