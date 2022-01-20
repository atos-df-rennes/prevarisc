$(document).ready(function() {
    $('.add-rubrique').on('click', function() {
        const form = this.closest('form')
        const capsuleRubrique = form.closest('div').id

        const formData = $(form).serialize()

        $.ajax({
            url: window.location.href,
            data: formData+'&capsule_rubrique='+capsuleRubrique,
            type: 'POST',
            success: function() {
                location.reload()
            },
            error: function() {
                return false
            }
        })
        return false
    })

    $('.delete-rubrique').on('click', function() {
        const id = this.getAttribute('data-id')

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
    })
})