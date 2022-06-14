$(document).ready(function() {
    $('.btnAddRowTable').click(function () { 
        $.ajax({
            type: "POST",
            url: "/formulaire/add-row-table",
            data: {ID_CHAMP:124,ID_ENTITY:14,ENTITY:"Etablissement",idx:42},
            dataType: "json",
            success: function (response) {
                console.log('Reponse : ',response)
            }
        });
    });


    $('.deleteRow').click(function (e) { 
        $.ajax({
            type: "POST",
            url: "/formulaire/delete-row-table",
            data: {ID_CHAMP_PARENT:$(this).attr('idParent'), idx:$(this).attr('idx')},
            dataType: "dataType",
            success: function (response) {
                console.log("Response : ",response)
            }
        });
    });
})