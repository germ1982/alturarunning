(function () {
    var entrada = document.getElementById('eventos-filtro');
    var tarjetas = Array.prototype.slice.call(document.querySelectorAll('[data-busqueda]'));
    var sinResultados = document.getElementById('eventos-sin-resultados');

    function normalizar(texto) {
        return texto.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
    }

    var indice = tarjetas.map(function (tarjeta) {
        return normalizar(tarjeta.getAttribute('data-busqueda'));
    });

    entrada.addEventListener('input', function () {
        var terminos = normalizar(entrada.value).split(/\s+/).filter(Boolean);
        var visibles = 0;

        tarjetas.forEach(function (tarjeta, i) {
            var coincide = terminos.every(function (termino) {
                return indice[i].indexOf(termino) !== -1;
            });

            tarjeta.classList.toggle('d-none', !coincide);
            visibles += coincide ? 1 : 0;
        });

        sinResultados.classList.toggle('d-none', visibles > 0);
    });
})();
