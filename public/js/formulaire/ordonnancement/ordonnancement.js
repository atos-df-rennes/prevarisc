window.onload = function(){

    const listGrp = document.getElementsByClassName('grp')
    const listParent = document.getElementsByClassName('parent')
    const listMain = document.getElementsByClassName('main')


    const getLisOrderInput = (idInputElem = null) => {
        const listChampClass = ['inputChamp', 'parent']

        console.log(document.getElementById(idInputElem))
        console.log(Array.from(document.getElementById(idInputElem).children).filter(ch => listChampClass.includes(ch.className)))
    }

    for(let i = 0; i< listGrp.length; i++){
        new Sortable.create(document.getElementById(listGrp[i].id), {
            group: listGrp[i].id,
            animation: 100
          });

          document.getElementById(listGrp[i].id).addEventListener(
              'dragend',
              function(){
                    //listInputOrder(document.getElementById(listGrp[i].id))
                    getLisOrderInput(listGrp[i].id)
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