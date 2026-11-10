/**
 * Comprime las fotos en el navegador apenas se eligen, antes de subirlas.
 *
 * Se activa en cualquier <input type="file" data-comprimir="ANCHO_MAXIMO"> (opcional:
 * data-calidad="0.8"), incluso en los que se agregan después a la página. Los archivos
 * que no son fotos (por ejemplo, PDF) se suben tal cual.
 *
 * El servidor vuelve a optimizar al guardar (helpers/ImagenHelper.php), así que si este
 * script no corre o falla, la foto igual queda liviana.
 */
(function () {
    var TIPOS = ['image/jpeg', 'image/png', 'image/webp'];
    var pendientes = new WeakMap();

    function cargarImagen(archivo) {
        return new Promise(function (resolver, rechazar) {
            var url = URL.createObjectURL(archivo);
            var imagen = new Image();

            imagen.onload = function () {
                URL.revokeObjectURL(url);
                resolver(imagen);
            };
            imagen.onerror = function () {
                URL.revokeObjectURL(url);
                rechazar();
            };
            imagen.src = url;
        });
    }

    function comprimir(archivo, anchoMaximo, calidad) {
        if (TIPOS.indexOf(archivo.type) === -1) {
            return Promise.resolve(archivo);
        }

        // El navegador ya aplica la orientación del celular al dibujar la imagen.
        return cargarImagen(archivo).then(function (imagen) {
            var escala = Math.min(1, anchoMaximo / imagen.naturalWidth);
            var lienzo = document.createElement('canvas');

            lienzo.width = Math.round(imagen.naturalWidth * escala);
            lienzo.height = Math.round(imagen.naturalHeight * escala);
            lienzo.getContext('2d').drawImage(imagen, 0, 0, lienzo.width, lienzo.height);

            return new Promise(function (resolver) {
                lienzo.toBlob(function (blob) {
                    // Si no se achicó y no pesa menos, conviene quedarse con el original.
                    if (!blob || (escala === 1 && blob.size >= archivo.size)) {
                        resolver(archivo);
                        return;
                    }

                    resolver(new File([blob], archivo.name, { type: archivo.type, lastModified: Date.now() }));
                }, archivo.type, calidad);
            });
        }).catch(function () {
            return archivo;
        });
    }

    function tamanio(bytes) {
        return bytes >= 1048576 ? (bytes / 1048576).toFixed(1).replace('.', ',') + ' MB' : Math.round(bytes / 1024) + ' KB';
    }

    function estado(entrada, texto) {
        var aviso = entrada.nextElementSibling;

        if (!aviso || !aviso.classList.contains('compresion-estado')) {
            aviso = document.createElement('div');
            aviso.className = 'form-text compresion-estado';
            entrada.insertAdjacentElement('afterend', aviso);
        }

        aviso.textContent = texto;
    }

    function marcarFormulario(formulario, cambio) {
        if (!formulario) {
            return;
        }

        var cantidad = (pendientes.get(formulario) || 0) + cambio;
        pendientes.set(formulario, cantidad);

        formulario.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(function (boton) {
            boton.disabled = cantidad > 0;
        });
    }

    document.addEventListener('change', function (evento) {
        var entrada = evento.target;

        if (!entrada.matches || !entrada.matches('input[type="file"][data-comprimir]') || typeof DataTransfer === 'undefined') {
            return;
        }

        var archivos = Array.prototype.slice.call(entrada.files);

        if (archivos.length === 0) {
            return;
        }

        var anchoMaximo = parseInt(entrada.getAttribute('data-comprimir'), 10) || 1280;
        var calidad = parseFloat(entrada.getAttribute('data-calidad') || '0.8');
        var antes = archivos.reduce(function (total, archivo) { return total + archivo.size; }, 0);

        estado(entrada, 'Optimizando ' + (archivos.length === 1 ? 'la foto' : 'las fotos') + '...');
        marcarFormulario(entrada.form, 1);

        Promise.all(archivos.map(function (archivo) {
            return comprimir(archivo, anchoMaximo, calidad);
        })).then(function (listos) {
            var despues = listos.reduce(function (total, archivo) { return total + archivo.size; }, 0);

            try {
                var transferencia = new DataTransfer();
                listos.forEach(function (archivo) { transferencia.items.add(archivo); });
                entrada.files = transferencia.files;
                estado(entrada, despues < antes ? 'Listo: ' + tamanio(antes) + ' → ' + tamanio(despues) : 'Listo para subir.');
            } catch (error) {
                estado(entrada, 'Se sube sin optimizar; el servidor la achica al guardar.');
            }

            marcarFormulario(entrada.form, -1);
        });
    });

    // Que no se envíe con Enter mientras todavía se están optimizando fotos.
    document.addEventListener('submit', function (evento) {
        if ((pendientes.get(evento.target) || 0) > 0) {
            evento.preventDefault();
            evento.stopImmediatePropagation();
        }
    }, true);
})();
