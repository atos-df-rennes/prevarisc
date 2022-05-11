window.onload = function(){

    const listGrp = document.getElementsByClassName('grp')
    const listParent = document.getElementsByClassName('parent')
    const listMain = document.getElementsByClassName('main')

    var originList = []


    const getLisOrderInput = (idInputElem = null) => {
        const listChampClass = ['inputChamp', 'parent','TR']
        //console.log(document.getElementById(idInputElem))
        //console.log(Array.from(document.getElementById(idInputElem).children).filter(ch => listChampClass.includes(ch.tagName)))
        
        let toReturn = []
        Array
            .from(document.getElementById(idInputElem).children)
                .filter(ch => listChampClass.includes(ch.tagName)).forEach(el =>{
                    toReturn.push({
                        //Nouvelle position
                        idx : (Array.from(document.getElementById(idInputElem).children).filter(ch => listChampClass.includes(ch.tagName))).indexOf(el),
                        ID_CHAMP : el.id
                    })
                })
        //console.log('To return : ',toReturn)
        //console.log(Array.from(document.getElementById(idInputElem).children).filter(ch => listChampClass.includes(ch.className)))
        return toReturn
    }
    
    //Retourne la liste des entity qui ont changé d'index
    const compareAndRequest = (newList = []) => {
        console.log("New data : ",newList.filter(elem => elem.ID_CHAMP != originList[elem.idx].ID_CHAMP ))
        newList.filter(elem => elem.ID_CHAMP != originList[elem.idx].ID_CHAMP ).forEach(champ =>{
            setNewIdxRequest(champ)
        })
    }

    const setNewIdxRequest = (objData = {}) =>{
        $.ajax({
            type: "post",
            url: "/formulaire/update-idx",
            data: objData,
            datatype:'json',
            success: function (response) {
                console.log("Success change index")
            }
        });
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