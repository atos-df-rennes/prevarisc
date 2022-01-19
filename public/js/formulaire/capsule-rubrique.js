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
})