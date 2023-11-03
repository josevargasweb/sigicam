class SMess {

    constructor() {

        //attributes
        this.messageQueue = [];
        this.messagesIsVisible = false;

        //init el contenedor
        this.makeMessageContainer(); 

    }


    /**
     * Crea el contenedor de mensajes en caso que no exista 
     */
    makeMessageContainer(){

        let isCreated = ($(".smess-container").length > 0) ? true : false;
        if(!isCreated){
            let messContainer = '<div class="smess-main-container"><div class="smess-info-container"><i class="bi bi-exclamation-triangle-fill"> <span class="badge">'+this.messageQueue.length+'</span></i></div><div class="smess-messages-container"></div></div>';
            $("body").append(messContainer);
        }

    }


    addQueueMessage(message){

        if(message != null && message.trim() != ""){

            this.messageQueue.push(message);
        }

    }

    showMessages(){

        $(".smess-info-container .badge ").html(this.messageQueue.length);

        if(this.messageQueue.length > 0){
            $(".smess-main-container").css("width", "25%");  
            let divMessAlertIsCreated = ($(".smess-message").length > 0) ? true : false;

            if(!divMessAlertIsCreated){
                let divMessAlert = '<div class="smess-message alert alert-danger" role="alert" ><p class="smess-message-title">Alergias</p><p class="mess-message-body"><ul class="mess-message-datalist"></ul></p></div>';
                $(".smess-messages-container").append(divMessAlert);

            }

            $.each(this.messageQueue, function( index, msg ) {
                let m = "<li>"+msg+"</li>";
                $(".mess-message-datalist").append(m);
            });

            this.messagesIsVisible = true;            

        }

    }

    hideMessages(){
        if(this.messageQueue.length > 0){
        
            $(".smess-main-container").css("width", "0%");
            $(".smess-messages-container ").empty();
            this.messagesIsVisible = false;

        }
    }

}
