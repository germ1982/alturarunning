(function () {
    var formulario = document.getElementById('form-alumno');
    var personaExistente = document.getElementById('persona-existente');
    var contador = 0;

    function agregarFila(boton) {
        contador += 1;

        var html = document.getElementById(boton.getAttribute('data-agregar')).innerHTML
            .split('__K__').join('n' + Date.now() + contador);
        var plantilla = document.createElement('template');

        plantilla.innerHTML = html.trim();
        document.getElementById(boton.getAttribute('data-lista')).appendChild(plantilla.content.firstElementChild);
    }

    function quitarFila(boton) {
        var bloque = boton.closest('[data-bloque]');
        var eliminar = bloque.querySelector('[data-eliminar]');

        // Lo que todavía no se guardó desaparece; lo existente se marca y se puede deshacer.
        if (!eliminar) {
            bloque.remove();
            return;
        }

        var marcado = eliminar.value !== '1';
        eliminar.value = marcado ? '1' : '';
        bloque.classList.toggle('marcado-eliminar', marcado);
        boton.textContent = marcado ? 'Deshacer' : 'Quitar';
    }

    formulario.addEventListener('click', function (evento) {
        var agregar = evento.target.closest('[data-agregar]');
        var quitar = evento.target.closest('[data-quitar]');

        if (agregar) {
            agregarFila(agregar);
        } else if (quitar) {
            quitarFila(quitar);
        }
    });

    if (personaExistente) {
        personaExistente.addEventListener('change', function () {
            document.getElementById('datos-persona').classList.toggle('d-none', personaExistente.value !== '');
        });
    }
})();
