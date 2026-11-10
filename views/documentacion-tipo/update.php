<?php

/** @var yii\web\View $this */
/** @var app\models\DocumentacionTipo $tipo */

$this->title = 'Editar tipo de documentación';

echo $this->render('_form', ['tipo' => $tipo]);
