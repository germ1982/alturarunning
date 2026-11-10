(function () {
    var MAX_ARCHIVOS = 20;
    var MAX_BYTES = 38 * 1024 * 1024;
    var formulario = document.getElementById('form-evento');
    var contador = 0;

    function nuevaClave() {
        contador += 1;
        return 'n' + Date.now() + contador;
    }

    function crearDesdePlantilla(id, reemplazos) {
        var html = document.getElementById(id).innerHTML;

        Object.keys(reemplazos).forEach(function (marca) {
            html = html.split(marca).join(reemplazos[marca]);
        });

        var plantilla = document.createElement('template');
        plantilla.innerHTML = html.trim();

        return plantilla.content.firstElementChild;
    }

    function quitar(boton) {
        var bloque = boton.closest('[data-bloque]');
        var eliminar = Array.prototype.find.call(bloque.querySelectorAll('[data-eliminar]'), function (campo) {
            return campo.closest('[data-bloque]') === bloque;
        });

        // Lo que todavía no se guardó desaparece; lo existente se marca y se puede deshacer.
        if (!eliminar) {
            bloque.remove();
            return;
        }

        var marcado = eliminar.value !== '1';
        eliminar.value = marcado ? '1' : '';
        bloque.classList.toggle('marcado-eliminar', marcado);
        boton.textContent = marcado ? 'Deshacer' : boton.getAttribute('data-texto');
    }

    formulario.querySelectorAll('[data-quitar]').forEach(function (boton) {
        boton.setAttribute('data-texto', boton.closest('details[data-bloque]') === boton.closest('[data-bloque]') ? 'Eliminar desafío' : 'Quitar');
    });

    document.getElementById('agregar-desafio').addEventListener('click', function () {
        var desafio = crearDesdePlantilla('tpl-desafio', { __D__: nuevaClave() });

        document.getElementById('lista-desafios').appendChild(desafio);
        desafio.querySelector('input[type="text"]').focus();
    });

    formulario.addEventListener('click', function (evento) {
        var agregar = evento.target.closest('[data-agregar-inscripcion]');
        var boton = evento.target.closest('[data-quitar]');

        if (agregar) {
            var fila = crearDesdePlantilla('tpl-inscripcion', {
                __D__: agregar.getAttribute('data-agregar-inscripcion'),
                __I__: nuevaClave()
            });

            agregar.closest('[data-bloque]').querySelector('[data-lista-inscripciones]').appendChild(fila);
        } else if (boton) {
            quitar(boton);
        }
    });

    formulario.addEventListener('submit', function (evento) {
        var cantidad = 0;
        var bytes = 0;

        formulario.querySelectorAll('input[type="file"]').forEach(function (entrada) {
            Array.prototype.forEach.call(entrada.files, function (archivo) {
                cantidad += 1;
                bytes += archivo.size;
            });
        });

        if (cantidad > MAX_ARCHIVOS || bytes > MAX_BYTES) {
            evento.preventDefault();
            mostrarAlerta('Podés subir hasta ' + MAX_ARCHIVOS + ' fotos y 38 MB por vez. Guardá en dos tandas.', 'Demasiadas fotos');
        }
    });
})();
