<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

$this->title = 'Solicitar reseteo de contraseña';
$this->params['breadcrumbs'][] = $this->title;

// Creamos el DataProvider
$dataProvider = new ActiveDataProvider([
    'query' => \app\models\User::find()
        ->joinWith('roles')
        ->where(['user_rol.nombre' => ['Administrador', 'Moderador']])
        ->distinct(),
]);
?>

<div class="site-about" style="max-width: 900px; margin: 0 auto; font-family: inherit;">
    
    <div class="grupos-header mb-4">
        <img src="<?= Url::to('@web/img/banner_comunidad.jpg')?>" alt="Header Grupos" class="grupos-header__img w-100" style="background: #f0f0f0; object-fit: cover;">
    </div>

    <h1 style="color: #b8860b; border-bottom: 2px solid #deb887; padding-bottom: 10px; margin-bottom: 25px;"><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-info" style="margin-bottom: 30px;">
        <p style="margin: 0; font-weight: bold;">Te puedes comunicar con alguno de los administradores o moderadores para solicitar tu reseteo de contraseña.</p>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'tableOptions' => ['class' => 'table table-bordered table-striped'],
        'columns' => [
            'username',
            'email:email',
            [
                'attribute' => 'telefono',
                'format' => 'raw',
                'value' => function ($model) {
                    $num = preg_replace('/[^0-9]/', '', $model->telefono);
                    $waLink = "https://wa.me/" . (str_starts_with($num, '54') ? $num : '549' . $num);
                    return Html::a('<i class="bi bi-whatsapp"></i> Contactar', $waLink, [
                        'target' => '_blank', 
                        'class' => 'btn btn-success btn-sm'
                    ]);
                },
            ],
        ],
    ]); ?>

</div>