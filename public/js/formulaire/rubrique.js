$(document).ready(function(){
    $("button").on('click', function() {
        buttonForm = this.closest("form")
        capsuleRubrique = buttonForm.closest("div").id

        buttonFormData = $(buttonForm).serialize()

        $.ajax({
            url: "/formulaire",
            data: buttonFormData+'&capsule_rubrique='+capsuleRubrique,
            type:"POST",
            success: function(){
                window.location.href = '/formulaire'
            },
            error: function(){
                return false;
            }
        });
        return false;
    });
});