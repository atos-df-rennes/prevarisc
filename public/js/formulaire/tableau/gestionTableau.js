$(document).ready(function() {  
    
    $('.addRow').click(function() {
        const ligneTableauTemplate = $('#tbody-'+$(this).attr('idparent') + ' tr:first-child').clone().removeClass('ligneTableau hidden')    
           
        $('#tbody-'+$(this).attr('idparent')).append(ligneTableauTemplate)

        const parentTableBody = document.getElementById('tbody-'+$(this).attr('idparent'))
        const parentTableNewLine = parentTableBody.getElementsByTagName('tr')[
            document.getElementById('tbody-'+$(this).attr('idparent'))
                    .getElementsByTagName('tr').length -1
        ]

        //recuperation du tr comprennant tous les inputs
        const inputsInNewLine = parentTableNewLine.getElementsByTagName('input')
        const textareasInNewLine = parentTableNewLine.getElementsByTagName('textarea')
        const selectsInNewLine = parentTableNewLine.getElementsByTagName('select')

        const formElements = [inputsInNewLine, textareasInNewLine, selectsInNewLine]

        //Creation du timestamp pour grouper la ligne des inputs
        const newD = Date.now()

        formElements.forEach(formElement => {
            Array.from(formElement).forEach(input => {
                if(input.name){
                    let strSplit = input.name.split('-')
                    newStr = strSplit[0] + '-' + newD + '-' + strSplit[2] + '-' + strSplit[3] + '-NULL'
                    input.setAttribute("name",newStr)
                }
            })
        })

        $('.deleteRow').click(function(){
            $(this).closest('tr').remove();
        })

    })

    $('.deleteRow').click(function(){
        $(this).closest('tr').remove();
    })

})