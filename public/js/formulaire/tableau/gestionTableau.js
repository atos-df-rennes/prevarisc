$(document).ready(function() {  
    
    $('.addRow').click(function() {
           
        //Ajout de la nouvelle ligne au tableau selectionne
        document.getElementById('tbody-'+$(this).attr('idparent')).innerHTML 
        +=
            ( 
                document.getElementById('tbody-'+$(this).attr('idparent')).children[0].innerHTML
            )
        
        //recuperation du tr comprennant tous les inputs
        let inputInTr = 
            document.getElementById('tbody-'+$(this).attr('idparent'))
                        .getElementsByTagName('tr')
                            [document.getElementById('tbody-'+$(this).attr('idparent'))
                                        .getElementsByTagName('tr').length -1 ]
                                        .getElementsByTagName('input')
        //Creation du timestamp pour grouper la ligne des inputs
        const newD = Date.now()
        Array.from(inputInTr).forEach(input => {
            if(input.name){
                let strSplit = input.name.split('-')
                newStr = strSplit[0] + '-' + newD + '-' + strSplit[2] + '-' + strSplit[3] + '-NULL'
                input.setAttribute("name",newStr)
            }
        })

        $('.deleteRow').click(function(){
            $(this).closest('tr').remove();
        })

    })

    $('.deleteRow').click(function(){
        $(this).closest('tr').remove();
    })

})