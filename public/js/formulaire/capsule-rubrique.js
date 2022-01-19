$(document).ready(function() {
    $('.rubrique-form button').on('click', function() {
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

        $.ajax({
            url: '/formulaire/delete-rubrique/rubrique/'+id,
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