    var loader = document.querySelector("#pageloader");
    var title = loader.firstChild;
    var inputMessage = document.querySelector("#ChatRoomSendInput");
    var buttonSend = document.querySelector("#ChatRoomSendBtn");
    var ChatBox = document.querySelector("#ChatBox");

    ChatBox.scrollTop = ChatBox.scrollHeight;

    function startWebsocket() {
        loader.classList.remove("is-danger");
        loader.classList.remove("is-success");
        loader.classList.add("is-warning");
        title.innerText = "Conectando no servidor de chat";

        var wss = new WebSocket('wss://philadelpho.tk:8443/?ustoken=' + ustoken + '&t=' + t + '&setoken=' + setoken)

        wss.onopen = function(e) {
            loader.classList.remove("is-warning");
            loader.classList.remove("is-danger");
            loader.classList.add("is-success");
            title.innerText = "Conectado. Aguardando resposta";
            setTimeout(() => {
                loader.classList.remove("is-active")
                loader.classList.remove("is-success");
            }, 1000);
        }
        wss.onmessage = function(e){
            showMessages('other', e.data);
        }
      
        wss.onclose = function(e){
            loader.classList.remove("is-active");
            loader.classList.remove("is-warning");
            loader.classList.remove("is-success");
            loader.classList.add("is-danger");
            loader.classList.add("is-active");

            if(e.code == 3000) {
                title.innerText = "O chat foi desconectado porque você conectou na mesma conversa em outro local";
            }
            else if(e.code == 3001) {
                title.innerText = "Ocorreu um erro. Por favor, tente novamente mais tarde";
            }
            else if(e.code == 1006) {
                title.innerText = "Desconectado. Reconectando em 5 segundos";
                setTimeout(reloadPage, 5000);
            }  
        }

        buttonSend.addEventListener('click', function(){
            if (inputMessage.value != '' && wss.readyState != 3 && wss.readyState != 2 && wss.readyState != 0) {
                var msg = {'msg': inputMessage.value};
                msg = JSON.stringify(msg);
    
                wss.send(msg);
                showMessages('me', msg);
                inputMessage.value = '';
            }
        });
    
        inputMessage.addEventListener('keydown', function(e){
            if (inputMessage.value != '' && e.keyCode == 13 && wss.readyState != 3 && wss.readyState != 2 && wss.readyState != 0) {
                var msg = {'msg': inputMessage.value};
                msg = JSON.stringify(msg);
    
                wss.send(msg);
                showMessages('me', msg);
                inputMessage.value = '';
            }
        });

        function showMessages(how, data) {
            data = JSON.parse(data);
    
            var br = document.createElement("br");
    
            if(how == "me") {
                var card = document.createElement("div");
                card.classList.add("card", "has-text-right");
                card.style = "background: rgba(123, 63, 212);";
            }
            else if(how == "other") {
                var card = document.createElement("div");
                card.classList.add("card", "has-text-left");
            }
    
            var cardcontent = document.createElement("div");
            cardcontent.classList.add("card-content");
    
            var content = document.createElement("div");
            content.classList.add("content");
    
            var submessage = document.createElement("div");
            submessage.classList.add("subtitle", "is-5");
            submessage.innerText = data.msg;
    
            var subdate = document.createElement("p");
            subdate.classList.add("subtitle", "is-6");
            subdate.innerText = new moment().format("DD/MM/yyyy HH:mm");

            if(how == "me") {
                submessage.classList.add("has-text-white");
                subdate.classList.add("has-text-white");
            }
    
            content.appendChild(submessage);
            content.appendChild(subdate);
    
            cardcontent.appendChild(content);
            
            card.appendChild(cardcontent);
    
            ChatBox.appendChild(br);
            ChatBox.appendChild(card);
    
            ChatBox.scrollTop = ChatBox.scrollHeight;
        }
    }

    function reloadPage() {
        window.location.replace("");
    }
    
    startWebsocket();


    