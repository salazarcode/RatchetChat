let socket = new WebSocket("ws://localhost:5001");

socket.onopen = function (e) {
    console.log("[open] Conexión establecida");
    console.log("Enviando al servidor");
    socket.send(JSON.stringify({
        "procedure" : "GET_CHANNEL",
        "channel_id" : 12
    }));
};

/** Escribiendo eventos del chat */

// enviar el mensaje del form
document.forms.publish.onsubmit = function (e) {
    e.preventDefault();
    let outgoingMessage = {
        "procedure" : "ADD_MESSAGE",
        "channel_id" : 12,
        "usuario_id" : 1,
        "text" : this.message.value
    }

    console.log(outgoingMessage);
    socket.send(JSON.stringify(outgoingMessage));
    this.message.value = ''
    return false;
};

// mensaje recibido - muestra el mensaje en div#messages
socket.onmessage = function (event) {
    let message = event.data;

    let messageElem = document.createElement('div');
    messageElem.innerHTML = `
    <div class="chat-message-group has-margin-top-10">
        <div class="notification">
            <p>${message}</p>
            <div class="from has-text-right has-text-grey is-size-7">A las 04:55 por <b>Adrián Salvatori</b></div>
        </div>
    </div>
    `;
    document.getElementById('messages').prepend(messageElem);
}

socket.onclose = function (event) {
    if (event.wasClean) {
        console.log(`[close] Conexión cerrada limpiamente, código=${event.code} motivo=${event.reason}`);
    } else {
        // ej. El proceso del servidor se detuvo o la red está caída
        // event.code es usualmente 1006 en este caso
        console.log('[close] La conexión se cayó');
    }
};

socket.onerror = function (error) {
    console.log(`[error] ${error.message}`);
};


