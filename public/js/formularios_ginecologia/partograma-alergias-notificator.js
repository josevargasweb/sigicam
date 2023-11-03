
var sMess = new SMess();


function ajaxCallAlergiaData(){


   return new Promise((resolve, reject) => {

    $.ajax({
        url: alergiaDataURL,
        data:  {caso_id: caso_id},
        dataType: "json",
        type: "post",
        success: function(data){
            resolve(data);
        },
        error: function(request, status, error){
            reject(request, status, error);

        },
    });
});

}

function initAlergiasMessages(){

    ajaxCallAlergiaData()
    .then((data) => {

        try {

            //-- GET DATA -- //
            let alergias =  JSON.parse(data.alergiaData).alergias;
            alergias.forEach(function(item, i) {

                let alergiaObs = item.alergiaObs;
                if(alergiaObs != null && item.alergia == "si"){
                    //agrega mensajes a la cola
                    sMess.addQueueMessage(alergiaObs);
                }

            });

            //muestra mensajes
            sMess.showMessages();

        }
        catch(e){
            console.log(e);
            bootbox.alert("Ha ocurrido un error.");
        }

    })
    .catch((request, status, error) => {
        bootbox.alert("Ha ocurrido un error.");
    });


}




$(function() {
    
    //get alergias messages
    initAlergiasMessages();


    $(document).on('click', '.smess-info-container', function() {

        let messagesIsVisible = sMess.messagesIsVisible;

        if(messagesIsVisible){
            sMess.hideMessages();
        }
        else {
            sMess.showMessages();
        }

    });

});