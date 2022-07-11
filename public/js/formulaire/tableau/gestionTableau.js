$(document).ready(function() {  
    
    $('.addRow').click(function() {
        //Envoie un ajout de ligne en db a supprimer une fois fini
        /*
        $.ajax({
            type: "POST",
            url: "/formulaire/add-row-table",
            data: {ID_CHAMP:$(this).attr('idparent'),ID_ENTITY:document.location.href.split('/')[6],ENTITY:document.location.href.split('/')[3].charAt(0).toUpperCase()+document.location.href.split('/')[3].slice(1) ,idx:document.getElementById('tbody-'+$(this).attr('idparent')).children.length},
            dataType: "json"
        }).then(() => {*/
            
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
                input.setAttribute("name",'valeur-'+newD+'-'+input.name.split('-')[input.name.split('-').length - 2]+'-'+input.name.split('-')[input.name.split('-').length - 1])
            }
        })

        $('.deleteRow').click(function(){
            $(this).closest('tr').remove();
        })

        //})
    })

    $('.deleteRow').click(function(){
        $(this).closest('tr').remove();
    })
    
    $('.deleteRow').click(function () { 
        $.ajax({
            type: "POST",
            url: "/formulaire/delete-row-table",
            data: {ID_CHAMP_PARENT:$(this).attr('idParent'), idx:$(this).attr('idx'), ENTITY: document.location.href.split('/')[3].charAt(0).toUpperCase()+document.location.href.split('/')[3].slice(1), ID_ENTITY:document.location.href.split('/')[6]},
            dataType: "dataType"
        });
    });
})