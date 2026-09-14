window.mostrarAlerta = function(mensaje, titulo = 'Información', type = 'red', onClose = null, imageUrl = null) {
    if (!imageUrl) {
        imageUrl = window.baseUrl + '/images/sistema/dinomanunciando2.png';
    }

    let content = `
        <div class="dino-alert-container">
            <img src="${imageUrl}" alt="dino" class="dino-alert-img">
            <div class="dino-alert-text">${mensaje}</div>
        </div>
    `;

    $.alert({
        title: titulo,
        content: content,
        type: type,
        theme: 'dark',
        animation: 'none',
        closeAnimation: 'none',
        onDestroy: function() {
            if (typeof onClose === 'function') onClose();
        },
        onOpen: function() {
            this.$title.css({ 'text-align': 'center', 'width': '100%', 'color': '#ff3333' });
            this.$btnc.css({ 'display': 'flex', 'justify-content': 'center', 'width': '100%', 'float': 'none' });
        }
    });
};


window.mostrarConfirmacion = function(titulo, mensaje, onConfirm, imageUrl = null) {

    if (!imageUrl) {
        imageUrl = window.baseUrl + '/images/sistema/dinomanunciando2.png';
    }

    let content = `
        <div class="dino-alert-container">
            <img src="${imageUrl}" alt="dino" class="dino-alert-img">
            <div class="dino-alert-text">${mensaje}</div>
        </div>
    `;

    $.confirm({
        title: titulo,
        content: content,
        type: 'red',
        theme: 'dark',
        animation: 'none',
        closeAnimation: 'none',
        buttons: {
            confirmar: {
                text: 'Sí, obio',
                btnClass: 'btn-danger',
                action: function() {
                    if (typeof onConfirm === 'function') onConfirm();
                }
            },
            cancelar: {
                text: 'No, mejor no',
                btnClass: 'btn-secondary'
            }
        },
        onOpen: function() {
            this.$title.css({ 'text-align': 'center', 'width': '100%', 'color': '#ff3333' });
            this.$btnc.css({ 'display': 'flex', 'justify-content': 'center', 'width': '100%', 'float': 'none', 'gap': '10px' });
        }
    });
};