window.onload = function(){

    console.log("i'm load bro")

    const listGrp = document.getElementsByClassName('grp')
    const listParent = document.getElementsByClassName('parent')
    const listMain = document.getElementsByClassName('main')

    let originList = []

    const getLisOrderInput = function(idInputElem = null){
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
    const compareAndRequest = function(newList = []){
        newList.filter(elem => elem.ID != originList[elem.idx].ID ).forEach(champ =>{
            setNewIdxRequest(champ)
        })
    }

    const setNewIdxRequest = function(objData = {}){
        document.location.href.includes('edit') ? 
            $.ajax({
                type: "post",
                url: "/formulaire/update-champ-idx",
                data: objData,
                datatype:'json'
            }).then(console.log('Requete ok'))      
        :
            $.ajax({
                type: "post",
                url: "/formulaire/update-rubrique-idx",
                data: objData,
                datatype:'json'
            }).then(console.log('Requete ok'))
    }

    for(let i = 0; i< listGrp.length; i++){
        new Sortable.create(document.getElementById(listGrp[i].id), {
            group: listGrp[i].id,
            animation: 100,
            onEnd: function () {
                compareAndRequest(getLisOrderInput(listGrp[i].id))
            }
          });

/*
          document.getElementById(listGrp[i].id).addEventListener(
              'dragend',
              function(){
                    console.log("Dragend ...")
                    compareAndRequest(getLisOrderInput(listGrp[i].id))
                    console.log("new attribution done")
                },
              false
          )
*/
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



    document.addEventListener('dragend', function(){
        console.log('Drag end')
    })

    document.addEventListener('click', function(){
        console.log('Clic document')
    })

    document.addEventListener('mouseup', function(){
        console.log('Clic up')
    })
}