<?php

use yii\bootstrap5\Modal;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var app\models\User $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Usuarios';
$title_new = 'Nuevo Usuario';


?>

<div class="user-index container-fluid">
    <div class="card shadow-sm border-0">

        <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
            <div class="titulo-only-custom" style="margin-bottom: 0;"><?= Html::encode($this->title) ?></div>

            <?= Html::button('<i class="bi bi-plus-lg"></i> <span>Nuevo</span>', [
                'value' => Url::to(['create']),
                'class' => 'btn-plus showModalButton',
                'id' => 'modalButton',
                'data-title' => $title_new
            ]) ?>
        </div>

        <div class="px-4">
            <hr class="hr-custom">
        </div>

        <div class="card-body p-0">
            <?php Pjax::begin(['id' => 'user-pjax-container']); ?>
            <div id="ajaxCrudDatatable" class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'summary' => false,
                    'tableOptions' => [
                        //'class' => 'table table-bordered table-striped table-condensed custom-grid kv-table-wrap'
                        'class' => 'table table-bordered table-striped table-hover custom-grid '
                    ],
                    'columns' => require(__DIR__ . '/_columns.php'),
                ]); ?>
            </div>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>

<?php

// 1. Definimos el Modal de Bootstrap 5
Modal::begin([
    'id' => 'ajaxCrudModal',
    'title' => '<div id="modal-title" class="titulo-only-custom"></div>', // se puede cambiar dinámicamente
    'size' => Modal::SIZE_LARGE,

    //'footer' => '<div id="modal-footer" class="d-flex justify-content-between w-100 px-3"></div>',
    'footer' => '<div id="modal-footer" class="d-flex justify-content-ce
    nter w-100 px-3"></div>',
    'options' => [
        'class' => 'custom-modal shadow-lg', // <-- ACÁ AGREGÁS TU CLASE
    ],
]);
echo "<div id='modal-content'></div>";
//echo '<div id="modal-content"><div class="text-center"><div class="spinner-border text-primary" role="status"></div></div></div>';
Modal::end();

// 2. El JavaScript para manejar el Modal en BS5
$js = <<<JS
    
$(document).on('click', '.showModalButton', function() {
    var modal = $('#ajaxCrudModal');
    var url = $(this).attr('value');

    // 1. Limpiamos con selectores de CLASE (que son los que trae Bootstrap)
    modal.find('.modal-title').html('Cargando...'); 
    modal.find('.modal-body').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>');
    modal.find('#modal-footer').html(''); 
    
    modal.modal('show');

    $.get(url)
        .done(function(response) {
            // response es el JSON (Notación de Objetos de JavaScript) del Controller
            if (response.title) {
                modal.find('.modal-title').html(response.title);
            }
            if (response.content) {
                // Cambiamos #modal-content por .modal-body
                modal.find('.modal-body').html(response.content);
            }
            if (response.footer) {
                modal.find('#modal-footer').html(response.footer);
            }
        })
        .fail(function() {
            modal.find('.modal-body').html('<div class="alert alert-danger">Error crítico.</div>');
        });
});


$(document).on('submit', '#ajaxCrudModal form', function(e) {
    // 1. Evita que el navegador recargue la página (comportamiento por defecto)
    e.preventDefault();

    // 2. Referenciamos el formulario que se está enviando
    var form = $(this);
    
    // LOG DE CONTROL: ¿Se activó la función?
    console.log("1. Evento submit detectado en el modal.");
    console.log("2. ID del formulario capturado: " + form.attr('id'));
    console.log("3. URL de destino (action): " + form.attr('action'));

    // 3. Enviamos la petición por POST de forma asíncrona
    // form.serialize() empaqueta todos los inputs del form para el envío
    $.post(form.attr('action'), form.serialize())
        .done(function(result) {
            
            console.log("4. Respuesta recibida del servidor.");
            console.log("5. Tipo de dato recibido: " + typeof result);

            // 4. Si el controlador mandó un JSON (el nuevo estándar)
            if (typeof result === 'object') {
                
                console.log("6. Procesando respuesta como JSON (Éxito o Validación)");

                // Inyectamos el contenido y el footer que vienen del Controller

                $('#ajaxCrudModal .modal-title').html(result.title);
                $('#ajaxCrudModal .modal-body').html(result.content);
                $('#ajaxCrudModal #modal-footer').html(result.footer);
                
                // Si el JSON trae 'forceReload', refrescamos la grilla PJAX
                if (result.forceReload) {
                    console.log("7. Recargando contenedor PJAX: " + result.forceReload);
                    $.pjax.reload({
                        container: result.forceReload,
                        timeout: 5000
                    });
                }
            } 
            // 5. Si el controlador mandó HTML (el viejo estándar o error de Yii)
            else {
                console.log("6. Procesando respuesta como HTML (Error de validación o fallo)");
                $('#ajaxCrudModal .modal-body').html(result);
            }
        })
        .fail(function(xhr) {
            // LOG DE ERROR: Si el servidor explota o la URL no existe
            console.error("ERROR CRÍTICO: El servidor no pudo procesar la petición.");
            console.error("Estado: " + xhr.status);
        });

    // Evita cualquier otra acción del navegador
    return false;
});

// LA LIMPIEZA: Para que al abrirlo de nuevo no aparezca el cartel de "Guardado"
$(document).on('hidden.bs.modal', '#ajaxCrudModal', function () {
    $(this).find('.modal-title').html('');
    $(this).find('.modal-body').html('<div class="text-center">Cargando...</div>');
    $(this).find('#modal-footer').html('');
});
    

JS;
$this->registerJs($js);
?>