<?php
namespace app\helpers;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

class AppIndexGenericoHelper
{
      public static function renderIndex($view, $title, $pjaxId, $gridColumns, $dataProvider, $searchModel, $options = [])
      {
            $view->title = $title;
            $btnTitle = $options['btnTitle'] ?? 'Nuevo';
            $createUrl = $options['createUrl'] ?? Url::to(['create']);
            $btnAlta = $options['btnAlta'] ?? true;

            $html = '<div class="user-index container-fluid" mt-4>';
            $html .= '<div class="neon-container mt-5">';

            $html .= '<div class="d-flex justify-content-between align-items-center mb-3">';
            $html .= '<div class="neon-title-container">' . Html::encode($title) . '</div>';
            
            if ($btnAlta) {
                // Cambiado a Html::a para navegación estándar
                $html .= Html::a('<i class="bi bi-plus-lg"></i> <span>' . $btnTitle . '</span>', $createUrl, [
                    'class' => 'btn btn-outline-light'
                ]);
            }
            $html .= '</div>';

            $html .= '<div><hr style="border-color: rgba(255, 51, 51, 0.4);"></div>';

            $html .= '<div class="card-body p-0">';

            ob_start();
            Pjax::begin(['id' => $pjaxId]);
            echo '<div id="ajaxCrudDatatable" class="table-responsive">';
            echo GridView::widget([
                  'dataProvider' => $dataProvider,
                  'filterModel' => $searchModel,
                  'summary' => false,
                  'tableOptions' => ['class' => 'table table-stark align-middle mb-0'],
                  'columns' => $gridColumns,
            ]);
            echo '</div>';
            Pjax::end();
            $html .= ob_get_clean();

            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';

            return $html;
      }
}