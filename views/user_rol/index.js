$(document).on('click', '#btn-crear-rol', async function(e) {
    e.preventDefault();
    
    let inputNombre = $('#input-nombre-rol').val();
    
    if (!inputNombre.trim()) {
        mostrarAlerta('Tenés que ponerle un nombre al rol.', '¡Atención!', 'red');
        return;
    }
    
    // 1. Consultamos si ya existe de forma síncrona/lineal
    try {
        let resVerificar = await $.ajax({
            url: 'index.php?r=rol/verificar',
            type: 'get',
            data: { nombre: inputNombre },
            dataType: 'json'
        });

        if (resVerificar.existe) {
            mostrarAlerta("El rol '" + inputNombre + "' ya existe", '¡Atención!', 'red');
            return; // Corta acá de una, sin anidar nada
        }

        // 2. Si no existe, tiramos la confirmación
        mostrarConfirmacion(
            '¿Crear rol?', 
            `
            <div style="text-align: center;">
                Estás a punto de crear el rol: <b>${inputNombre}</b>
            </div>
            `, 
            async function() {
                let resCrear = await $.ajax({
                    url: 'index.php?r=rol/crear',
                    type: 'get',
                    data: { nombre: inputNombre },
                    dataType: 'json'
                });

                if (resCrear.success) {
                    mostrarAlerta(resCrear.mensaje, '¡Éxito!', 'green', function() {
                        location.reload();
                    });
                } else {
                    mostrarAlerta(resCrear.mensaje, '¡Atención!', 'red');
                }
            }
        );

    } catch (error) {
        mostrarAlerta('Ocurrió un error inesperado al procesar la solicitud.', 'Error', 'red');
    }
});

// Editar rol
$(document).on('click', '.btn-editar-rol', function() {
    let nombreActual = $(this).data('nombre');

    $.confirm({
        title: 'Editar Rol',
        content: '' +
            '<form action="" class="formName">' +
            '<div class="form-group">' +
            '<label>Nuevo nombre para el rol:</label>' +
            '<input type="text" value="' + nombreActual + '" class="form-control nuevo-nombre-input" required />' +
            '</div>' +
            '</form>',
        type: 'orange',
        theme: 'dark',
        buttons: {
            formSubmit: {
                text: 'Guardar',
                btnClass: 'btn-warning',
                action: function() {
                    let nuevoNombre = this.$content.find('.nuevo-nombre-input').val();
                    if (!nuevoNombre.trim()) {
                        mostrarAlerta('El nombre no puede estar vacío.', '¡Atención!', 'red');
                        return false;
                    }

                    $.ajax({
                        url: 'index.php?r=rol/update',
                        type: 'get',
                        data: { nombreOriginal: nombreActual, nuevoNombre: nuevoNombre },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                mostrarAlerta(response.mensaje, '¡Éxito!', 'green', function() {
                                    location.reload();
                                });
                            } else {
                                mostrarAlerta(response.mensaje, '¡Atención!', 'red');
                            }
                        }
                    });
                }
            },
            cancelar: {
                text: 'Cancelar',
                btnClass: 'btn-secondary'
            }
        },
        onOpen: function() {
            this.$title.css({ 'text-align': 'center', 'width': '100%', 'color': '#ff9900' });
            this.$btnc.css({ 'display': 'flex', 'justify-content': 'center', 'width': '100%', 'float': 'none', 'gap': '10px' });
        }
    });
});

// Eliminar rol
$(document).on('click', '.btn-eliminar-rol', function() {
    let nombreRol = $(this).data('nombre');

    mostrarConfirmacion(
        '¿Eliminar rol?', 
        'Estás a seguro de eliminar el rol: <b>' + nombreRol + '</b>', 
        async function() {
            let res = await $.ajax({
                url: 'index.php?r=rol/delete',
                type: 'get',
                data: { nombre: nombreRol },
                dataType: 'json'
            });

            if (res.success) {
                mostrarAlerta(res.mensaje, '¡Éxito!', 'green', function() {
                    location.reload();
                });
            } else {
                mostrarAlerta(res.mensaje, '¡Atención!', 'red');
            }
        }
    );
});