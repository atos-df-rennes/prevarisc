window.onload = function() {
    const listGrp = document.getElementsByClassName('grp')
    let originList = []

    const getListOrderInput = (idInputElem = null) => {
        let toReturn = []

        const tableRows = document.querySelectorAll('#' + idInputElem + ' tr')
        Array.from(tableRows).forEach(el => {
            toReturn.push({
                // Nouvelle position
                idx: Array.from(tableRows).indexOf(el),
                ID: el.id
            })
        })

        return toReturn
    }
    
    //Retourne la liste des entity qui ont changé d'index
    const compareAndRequest = (newList = []) => {
        newList.filter(elem => elem.ID !== originList[elem.idx].ID ).forEach(champ =>{
            setNewIdxRequest(champ)
        })
    }

    const setNewIdxRequest = (objData = {}) =>{
        document.location.href.includes('edit') ? 
            $.ajax({
                type: "post",
                url: "/formulaire/update-champ-idx",
                data: objData,
                datatype:'json'
            })        
        :
            $.ajax({
                type: "post",
                url: "/formulaire/update-rubrique-idx",
                data: objData,
                datatype:'json'
            })
    }

    for (let i = 0; i < listGrp.length; i++) {
        new Sortable.create(document.getElementById(listGrp[i].id), {
            group: listGrp[i].id,
            animation: 100
        })

        document.getElementById(listGrp[i].id).addEventListener(
            'dragend',
            function() {
                compareAndRequest(getListOrderInput(listGrp[i].id))
            },
            false
        )

        document.getElementById(listGrp[i].id).addEventListener(
            'dragstart',
            function() {
                originList = getListOrderInput(listGrp[i].id)
            },
            false
        )
    }
}