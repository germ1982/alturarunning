<?php

namespace app\helpers;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;;

use yii\web\View;

class AppComboDatoHelper
{
    public static function getComboDatoTipo($listaTipos, $formularioPrincipal = 'div_formulario_principal', $botonGuardar = 'btnGuardar', $botonCerrar = 'btnCerrar')
    {

        // 1. El Combo con el Botón pegado (+)
        // Pasamos las variables a la función JS del onclick
        $html = '<div class="form-group mb-3">
                    <label class="form-label">Tipo de Dato</label>
                    <div class="input-group">' .
            Html::dropDownList('Dato[idtipo]', null, $listaTipos, [
                'id' => 'input_dato_tipo',
                'class' => 'form-control form-select select-custom',
                'prompt' => 'Seleccione...'
            ]) .
            '<button class="btn-custom" type="button" id="btn-mostrar-alta" style="min-width: 40px !important;" 
                onclick="mostrar_alta_dato_tipo(\'' . $formularioPrincipal . '\', \'' . $botonGuardar . '\', \'' . $botonCerrar . '\')">+</button>
                    </div>
                </div>';

        return $html;
    }

    public static function getDivAltaDatoTipo($formularioPrincipal = 'div_formulario_principal', $botonGuardar = 'btnGuardar', $botonCerrar = 'btnCerrar')
    {
        // 2. Div de Alta con el botón Cancelar usando las variables
        $html = '<div id="div_alta_dato_tipo" style="display:none; border:1px solid #ccc; padding:15px; border-radius:8px; background:#f9f9f9; margin-top:10px;">
                    <label class="form-label" style="font-size:12px;">Nuevo Tipo de Dato</label>
                    <input type="text" id="nueva-desc-ajax" class="form-control mb-2" placeholder="Tipo Dato...">

                    <label class="form-label" style="font-size:12px;">Constante</label>
                    <input type="text" id="nueva-constante-ajax" class="form-control mb-2" placeholder="Constante" style="text-transform: uppercase;">
                    <br>
                    <div class="d-flex justify-content-between w-100 px-3">
                        <button type="button" onclick="mostrar_formulario_principal_dato(\'' . $formularioPrincipal . '\', \'' . $botonGuardar . '\', \'' . $botonCerrar . '\')" class="btn-custom">Cancelar</button>
                        <button type="button" id="btn-guardar-ajax" class="btn-custom"
                        onclick="guardar_nuevo_tipo_dato(\'' . $formularioPrincipal . '\', \'' . $botonGuardar . '\', \'' . $botonCerrar . '\')">
                        Guardar</button>
                    </div>
                </div>';

        return $html;
    }



    //$urlGuardar = Url::to(['dato/create-dato-tipo-ajax']); // Ajusta a tu controller

}
?>
<script>
    function mostrar_alta_dato_tipo(fp, bg, bc) {
        $('#' + fp).hide();
        $('#div_alta_dato_tipo').show();
        $('#' + bc).hide();
        $('#' + bg).hide();
    }

    function mostrar_formulario_principal_dato(fp, bg, bc) {
        $('#' + fp).show();
        $('#div_alta_dato_tipo').hide();
        $('#' + bc).show();
        $('#' + bg).show();
    }

    function guardar_nuevo_tipo_dato(fp, bg, bc) {
        var descripcion = $('#nueva-desc-ajax').val();
        var constante = $('#nueva-constante-ajax').val().toUpperCase().trim().replace(/\s+/g, '_');

        if (descripcion.trim() === "") {
            Swal.fire({
                icon: 'warning',
                title: '¡Atención!',
                text: 'Escribí una descripción.',
            });
            return;
        }

        if (constante.trim() === "") {
            Swal.fire({
                icon: 'warning',
                title: '¡Atención!',
                text: 'Escribí una constante.',
            });
            return;
        }

        $.ajax({
            //url: '/index.php?r=dato/createaux', // Usamos la variable correcta
            url: '<?= Url::to(['dato/create_dato_tipo_ajax']) ?>',
            type: 'POST',
            data: {
                descripcion: descripcion,
                constante: constante
            },
            success: function(response) {
                console.log(response); // Para depuración
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Guardado!',
                        text: 'El tipo de dato se cargó correctamente.',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // ACTUALIZAMOS EL COMBO (Importante para que aparezca lo nuevo)
                    var nuevaOpcion = new Option(response.descripcion, response.id, true, true);
                    $('#input_dato_tipo').append(nuevaOpcion).trigger('change');

                    // LIMPIAMOS Y VOLVEMOS
                    $('#nueva-desc-ajax').val('');
                    mostrar_formulario_principal_dato(fp, bg, bc);

                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'No se pudo guardar.',
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de servidor',
                    text: 'No se pudo comunicar con el controlador.',
                });
            }
        });
    }

    // 1. Cuando salís de Descripción -> Copia a Constante con prefijo TIPO_
    $(document).on('blur', '#nueva-desc-ajax', function() {
        var descripcion = $(this).val().trim();
        var campoConstante = $('#nueva-constante-ajax');

        if (descripcion !== "" && campoConstante.val().trim() === "") {
            var slug = descripcion.toUpperCase().replace(/\s+/g, '_');

            // Verificamos si ya empieza con TIPO_
            if (!slug.startsWith('TIPO_')) {
                slug = 'TIPO_' + slug;
            }

            campoConstante.val(slug);
        }
    });

    // 2. Mientras escribís en Constante -> Forzar Mayúsculas, Guiones y Prefijo
    $(document).on('blur', '#nueva-constante-ajax', function() {
        var valor = $(this).val().trim().toUpperCase().replace(/\s+/g, '_');

        if (valor !== "") {
            // Si el usuario escribió algo a mano, nos aseguramos que tenga el TIPO_
            if (!valor.startsWith('TIPO_')) {
                valor = 'TIPO_' + valor;
            }
            $(this).val(valor);
        }
    });

    // 3. Limpieza en tiempo real (solo espacios y minúsculas)
    $(document).on('input', '#nueva-constante-ajax', function() {
        var valor = $(this).val().replace(/\s+/g, '_').toUpperCase();
        $(this).val(valor);
    });
</script>