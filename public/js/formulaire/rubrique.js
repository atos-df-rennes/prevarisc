$(document).ready(function(){
    $("#field-form button").on('click', function() {
        const formData = $('#field-form').serialize()

        $.ajax({
            url: window.location.href,
            data: formData,
            type:"POST",
            success: function() {
                location.reload()
            },
            error: function() {
                return false;
            }
        });
        return false;
    });
});