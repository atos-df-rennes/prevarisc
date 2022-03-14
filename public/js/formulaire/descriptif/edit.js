$(document).ready(function() {
    $('input[type="checkbox"]').on('click', function() {
        const elemToToggle = $('#'+this.name)
        if (elemToToggle.hasClass('hide')) {
            elemToToggle.show()
            elemToToggle.removeClass('hide')
        } else {
            elemToToggle.hide()
            elemToToggle.addClass('hide')
        }
    })
})