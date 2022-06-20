$(document).ready(function() {  
    $('.btnAddRowTable').click(function () { 
        $.ajax({
            type: "POST",
            url: "/formulaire/add-row-table",
            data: {ID_CHAMP:$(this).attr('idparent'),ID_ENTITY:document.location.href.split('/')[6],ENTITY:document.location.href.split('/')[3].charAt(0).toUpperCase()+document.location.href.split('/')[3].slice(1) ,idx:document.getElementById('tbody-'+$(this).attr('idparent')).children.length},
            dataType: "json"
        })
    });

    $('.deleteRow').click(function () { 
        $.ajax({
            type: "POST",
            url: "/formulaire/delete-row-table",
            data: {ID_CHAMP_PARENT:$(this).attr('idParent'), idx:$(this).attr('idx'), ENTITY: document.location.href.split('/')[3].charAt(0).toUpperCase()+document.location.href.split('/')[3].slice(1), ID_ENTITY:document.location.href.split('/')[6]},
            dataType: "dataType"
        });
    });
})