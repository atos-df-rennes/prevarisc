window.onload = function(){

    const listGrp = document.getElementsByClassName('grp')
    const listParent = document.getElementsByClassName('parent')
    const listMain = document.getElementsByClassName('main')

    let originList = []

    const getLisOrderInput = (idInputElem = null) => {
        const listChampClass = ['inputChamp', 'parent','TR']
        let toReturn = []
        Array
            .from(document.getElementById(idInputElem).children)
                .filter(ch => listChampClass.includes(ch.tagName)).forEach(el =>{
                    toReturn.push({
                        //Nouvelle position
                        idx : (Array.from(document.getElementById(idInputElem).children).filter(ch => listChampClass.includes(ch.tagName))).indexOf(el),
                        ID : el.id
                    })
                })
        return toReturn
    }
    
    //Retourne la liste des entity qui ont changé d'index
    const compareAndRequest = (newList = []) => {
        newList.filter(elem => elem.ID != originList[elem.idx].ID ).forEach(champ =>{
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

    for(let i = 0; i< listGrp.length; i++){
        new Sortable.create(document.getElementById(listGrp[i].id), {
            group: listGrp[i].id,
            animation: 100
          });

          document.getElementById(listGrp[i].id).addEventListener(
              'dragend',
              function(){
                    compareAndRequest(getLisOrderInput(listGrp[i].id))
                },
              false
          )

          document.getElementById(listGrp[i].id).addEventListener(
            'dragstart',
            function(){
                originList = getLisOrderInput(listGrp[i].id)
              },
            false
        )
    }

    for(let i = 0; i< listParent.length; i++){
        new Sortable.create(document.getElementById(listParent[i].id), {
            group:listParent[i].id,
            animation: 100
          });
          document.getElementById(listParent[i].id).addEventListener(
            'dragend',
            function(){
                getLisOrderInput(listParent[i].id)
            },
            false
        )
    }
      
    for(let i = 0; i< listMain.length; i++){
        new Sortable.create(document.getElementById(listMain[i].id), {
            group:listMain[i].id,
            animation: 100
          });
    }

}