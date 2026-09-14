<?php

namespace app\helpers;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap5\Modal;
//use yii\grid\GridView;
use kartik\grid\GridView;
use yii\widgets\Pjax;

class AppIndexGenericoHelper
{
      /**
       * Renderiza un Index completo y profesional con soporte AJAX.
       * * @param \yii\web\View $view El objeto $this de la vista
       * @param string $title Título de la página
       * @param string $pjaxId ID único para el contenedor PJAX
       * @param array $gridColumns Array de configuración de columnas
       * @param object $dataProvider
       * @param object $searchModel
       * @param array $options Opciones extras (btnTitle, modalSize, etc)
       */
      public static function renderIndex($view, $title, $pjaxId, $gridColumns, $dataProvider, $searchModel, $options = [])
      {
            // 1. Configuración de Assets y Títulos
            $view->title = $title;
            $btnTitle = $options['btnTitle'] ?? 'Nuevo';
            $createUrl = $options['createUrl'] ?? Url::to(['create']);
            $modalSize = $options['modalSize'] ?? Modal::SIZE_LARGE;
            $btnAlta = $options['btnAlta'] ?? true;

            // 2. Registro del JavaScript para el Modal (BS5)
            self::registerModalJs($view);

            // 3. Inicio de la Estructura Visual (Card)
            $html = '<div class="user-index container-fluid">';
            $html .= '<div class="card shadow-sm border-0">';

            // Header de la Card
            $html .= '<div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">';
            $html .= '<div class="titulo-only-custom" style="margin-bottom: 0;">' . Html::encode($title) . '</div>';
            if ($btnAlta) {
                $html .= Html::button('<i class="bi bi-plus-lg"></i> <span>' . $btnTitle . '</span>', [
                    'value' => $createUrl,
                    'class' => 'btn-plus showModalButton',
                    'data-title' => 'Crear ' . $title
                ]);
            }
            $html .= '</div>';

            $html .= '<div class="px-4"><hr class="hr-custom"></div>';

            // Cuerpo de la Card (Grilla)
            $html .= '<div class="card-body p-0">';

            ob_start();
            Pjax::begin(['id' => $pjaxId]);
            echo '<div id="ajaxCrudDatatable" class="table-responsive">';
            echo GridView::widget([
                  'dataProvider' => $dataProvider,
                  'filterModel' => $searchModel,
                  'summary' => false,
                  'responsive' => true,
                  'responsiveWrap' => true,
                  'tableOptions' => ['class' => 'table table-bordered table-striped table-hover custom-grid'],
                  'columns' => $gridColumns,
            ]);
            echo '</div>';
            Pjax::end();
            $html .= ob_get_clean();

            $html .= '</div>'; // card-body
            $html .= '</div>'; // card
            $html .= '</div>'; // container

            // 4. Renderizado del Modal
            ob_start();
            Modal::begin([
                  'id' => 'ajaxCrudModal',
                  'title' => '<div id="modal-title" class="titulo-only-custom"></div>',
                  'size' => $modalSize,
                  'footer' => '<div id="modal-footer" class="d-flex justify-content-center w-100 px-3"></div>',
                  'options' => ['class' => 'custom-modal shadow-lg'],
            ]);
            echo "<div id='modal-content'></div>";
            Modal::end();
            $html .= ob_get_clean();

            return $html;
      }

      private static function registerModalJs($view)
      {
            $js = <<<JS
        $(document).off('click', '.showModalButton').on('click', '.showModalButton', function() {
            var modal = $('#ajaxCrudModal');
            var url = $(this).attr('value');
            var title = $(this).attr('data-title');

            modal.find('.modal-title').html(title ? title : 'Cargando...'); 
            modal.find('.modal-body').html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"></div></div>');
            modal.find('#modal-footer').html(''); 
            
            modal.modal('show');

            $.get(url).done(function(response) {
                if (response.title) modal.find('.modal-title').html(response.title);
                if (response.content) modal.find('.modal-body').html(response.content);
                if (response.footer) modal.find('#modal-footer').html(response.footer);
            }).fail(function() {
                modal.find('.modal-body').html('<div class="alert alert-danger">Error al cargar contenido.</div>');
            });
        });

        $(document).off('submit', '#ajaxCrudModal form').on('submit', '#ajaxCrudModal form', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                        url:         form.attr('action'),
                        type:        'POST',
                        data:        new FormData(form[0]),
                        processData: false,
                        contentType: false,
                  })
                .done(function(result) {
                    if (typeof result === 'object') {
                        $('#ajaxCrudModal .modal-title').html(result.title);
                        $('#ajaxCrudModal .modal-body').html(result.content);
                        $('#ajaxCrudModal #modal-footer').html(result.footer);
                        if (result.forceReload) {
                            $.pjax.reload({container: result.forceReload, timeout: 5000});
                        }
                    } else {
                        $('#ajaxCrudModal .modal-body').html(result);
                    }
                });
            return false;
        });
JS;
            $view->registerJs($js);
      }
}
