<?php

namespace app\helpers;

use yii\helpers\Html;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use kartik\time\TimePicker;
use yii\helpers\ArrayHelper;


class AppFormHelper
{
    public static function getInputSelect($form, $model, $atributo, $id_input, $datos, $iddatos, $descripciondatos, $label = null, $placeholder = null)
    {
        return $form->field($model, $atributo)->dropDownList(
            ArrayHelper::map($datos, $iddatos, $descripciondatos),
            [
                'id' => $id_input,
                'prompt' => $placeholder ?? 'Seleccione...',
                'class' => 'form-control form-select select-custom', // Clase nativa de Bootstrap 5 + personalizada
            ]
        )->label($label);
    }

    public static function getSelectConAlta($form, $model, $atributo, $data, $tituloModal, $urlAlta)
{
    // Generamos un ID único para el select para que el JS sepa dónde insertar el dato nuevo
    $idCombo = Html::getInputId($model, $atributo);

    return $form->field($model, $atributo, [
        'template' => "{label}\n<div class='input-group'>{input}\n{button}</div>\n{error}"
    ])->dropDownList($data, [
        'id' => $idCombo,
        'prompt' => 'Seleccione...',
        'class' => 'form-select select-custom'
    ])->parts['{button}'] = Html::button('+', [
        'class' => 'btn btn-primary',
        'value' => \yii\helpers\Url::to([$urlAlta]),
        'data-idselect2' => $idCombo, // El JS busca este ID para actualizar
        'onclick' => "showConfigAbm(this, '{$tituloModal}');",
        'tabindex' => '-1'
    ]);
}
    public static function getInputSelect2($form, $model, $atributo, $id_input, $datos, $iddatos, $descripciondatos, $label = null, $placeholder = null)
    {
        return $form->field($model, $atributo)->widget(Select2::class, [
            'data' => ArrayHelper::map($datos, $iddatos, $descripciondatos),
            'options' => [
                'id' => $id_input,
                'placeholder' => $placeholder,
                'multiple' => false,
            ],
            'pluginOptions' => [
                'allowClear' => true,
                // ESTO ES CLAVE: Si el ID del modal es diferente, cambialo acá
                'dropdownParent' => new \yii\web\JsExpression('$("#ajaxCrudModal")'),
            ],
        ])->label($label);
    }/*  */

    /**
     * Genera un DatePicker (Selector de Fecha) estandarizado.
     */
    public static function getInputFecha($form, $model, $atributo = 'fecha', $id_input = null, $label = null, $disabled = false)
    {
        $id_input = $id_input ?? "input_$atributo";

        return $form->field($model, $atributo)->widget(DatePicker::className(), [
            'language' => 'es',
            'layout' => '{picker}{input}',
            'options' => [
                'id' => $id_input,
                'disabled' => $disabled,
                'placeholder' => 'DD / MM / YYYY',
            ],
            'pluginOptions' => [
                'format' => 'dd/mm/yyyy',
                'todayHighlight' => true,
                'autoclose' => true,
            ]
        ])->label($label);
    }

    // ... podés seguir moviendo el de Hora (TimePicker) y el Select común acá.
}
