$(document).ready(function() {
    tinymce.init({
        selector: '.tinyarea',
        language: 'fr_FR',
        height: 150,
        menubar: false,
        statusbar: false,
        plugins: [
            'advlist autolink lists link image charmap print preview anchor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime media table contextmenu paste code'
        ],
        toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link',
        content_css: [
            '//www.tinymce.com/css/codepen.min.css'
        ]
    })

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