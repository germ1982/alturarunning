<?php

/** @var yii\web\View $this */
/** @var app\models\Cuota $cuota */

$this->title = 'Nueva cuota';

echo $this->render('_form', ['cuota' => $cuota]);
