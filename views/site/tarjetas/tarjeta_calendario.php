<?php

use app\components\CardWidget;
use app\models\ConstantesGlobales;
use yii\helpers\Html;

$calendario = (new \yii\db\Query())
    ->select(['c.idlugar', 'c.dia', 'c.horario', 'd.dato as modalidad'])
    ->from('{{%calendario}} c')
    ->innerJoin('{{%dato_sistema}} d', 'd.iddato = c.idmodalidad')
    ->all();

$instructores = (new \yii\db\Query())
    ->select(['p.nombre', 'p.apellido', 'i.especialidad', 'f.ruta'])
    ->from('{{%persona_instructor}} i')
    ->innerJoin('{{%persona}} p', 'p.idpersona = i.idpersona')
    ->leftJoin('{{%persona_foto}} f', 'f.idpersona = p.idpersona AND f.es_principal = 1')
    ->all();

// Construimos el HTML del listado de profesores
$htmlInstructores = '<div class="d-flex flex-column gap-2">';
foreach ($instructores as $inst) {
    $foto = $inst['ruta'] ? '@web/images/personas/' . $inst['ruta'] : '@web/images/sistema/default.png';
    $htmlInstructores .= '<div class="d-flex align-items-center gap-2">';

    // Contenedor para el efecto de zoom y desborde controlado
    $htmlInstructores .= '<div class="profesor-avatar-container">';
    $htmlInstructores .= Html::img($foto, ['class' => 'profesor-avatar']);
    $htmlInstructores .= '</div>';

    $htmlInstructores .= '<div>';
    $htmlInstructores .= '<span class="d-block" style="font-size: 0.75rem; line-height: 1;"><span class="text-danger fw-bold">PROFESOR:</span> <span class="text-white">' . Html::encode($inst['nombre'] . ' ' . $inst['apellido']) . '</span></span>';
    $htmlInstructores .= '<span class="d-block" style="font-size: 0.6rem;"><span class="text-danger">Especialidad:</span> <span class="text-warning">' . Html::encode($inst['especialidad']) . '</span></span>';
    $htmlInstructores .= '</div>';
    $htmlInstructores .= '</div>';
}
$htmlInstructores .= '</div>';

$datos = [];
foreach ($calendario as $row) {
    // Agrupamos por lugar, día y horario exacto para detectar duplicados de horario
    $idlugar = $row['idlugar'];
    $dia = $row['dia'];
    $horario = $row['horario'];
    $modalidad = $row['modalidad'];

    // Si ya existe este horario en este día y lugar, unimos las modalidades
    if (isset($datos[$idlugar][$dia][$horario])) {
        // Evitamos duplicar si por casualidad fuera exactamente la misma
        if (!in_array($modalidad, $datos[$idlugar][$dia][$horario])) {
            $datos[$idlugar][$dia][$horario][] = $modalidad;
        }
    } else {
        $datos[$idlugar][$dia][$horario] = [$modalidad];
    }
}

// Filtramos primero los lugares que tienen datos para saber cuál es el último
ConstantesGlobales::LUGARES;
$lugaresConDatos = array_filter(ConstantesGlobales::LUGARES, function ($idlugar) use ($datos) {
    return isset($datos[$idlugar]);
}, ARRAY_FILTER_USE_KEY);

$totalLugares = count($lugaresConDatos);
$contadorLugar = 0;
$htmlContent = '';

foreach ($lugaresConDatos as $idlugar => $nombreLugar) {
    $contadorLugar++;
    // Agregamos la línea inferior solo si no es el último elemento
    $borderClass = ($contadorLugar < $totalLugares) ? ' border-bottom border-secondary' : '';

    $htmlContent .= '<div class="py-3' . $borderClass . '">';

    // Nombre del lugar
    $htmlContent .= '<div class="text-light fw-bold mb-2">' . Html::encode($nombreLugar) . '</div>';

    // Contenedor flexible con gap más chico (gap-1 o gap-2)
    $htmlContent .= '<div class="d-flex flex-wrap gap-2">';

    foreach (ConstantesGlobales::DIAS as $diaId => $nombreDia) {
        if (!isset($datos[$idlugar][$diaId])) {
            continue;
        }

        // Bajamos el min-width y max-width para que entren cómodos al menos 3 o 4 por línea en el celu
        $htmlContent .= '<div class="px-1 py-1" style="min-width: 75px; flex: 1 1 auto; max-width: 110px;">';
        // Día arriba del horario
        $htmlContent .= '<div class="text-danger fw-bold mb-1" style="font-size: 0.65rem;">' . $nombreDia . '</div>';

        foreach ($datos[$idlugar][$diaId] as $horario => $modalidades) {
            $htmlContent .= '<div class="lh-sm mb-1">';
            $htmlContent .= '<span class="text-white d-block" style="font-size: 0.75rem;">' . Html::encode($horario) . '</span>';

            if (count($modalidades) > 1) {
                $textoModalidad = 'Mixta';
            } else {
                $textoModalidad = $modalidades[0];
            }

            $htmlContent .= '<span class="text-warning" style="font-size: 0.55rem;">(' . Html::encode($textoModalidad) . ')</span>';
            $htmlContent .= '</div>';
        }

        $htmlContent .= '</div>';
    }

    $htmlContent .= '</div>'; // fin flex-wrap
    $htmlContent .= '</div>'; // fin row del lugar
}
?>
<style>
    .bg-dark {
        --bs-bg-opacity: 1;
        background-color: transparent !important;
    }

    /* Contenedor circular con borde neón rojo */
    .profesor-avatar-container {
        position: relative;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid #dc3545;
        box-shadow: 0 0 8px rgba(220, 53, 69, 0.8), inset 0 0 4px rgba(220, 53, 69, 0.5);
        flex-shrink: 0;
        cursor: pointer;
    }

    /* Imagen normal dentro del círculo */
    .profesor-avatar {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
        transform-origin: center center; /* Fuerza a que el punto de escala sea el centro exacto */
    }

    /* Al hacer hover, se eleva y se agranda al 200% desde el centro sin desplazarse raro */
    .profesor-avatar-container:hover .profesor-avatar {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        transform: scale(2); /* Se expande exactamente al doble */
        z-index: 1000;
        border-radius: 50%;
        box-shadow: 0 0 15px rgba(220, 53, 69, 1), 0 0 25px rgba(220, 53, 69, 0.8);
        border: 2px solid #dc3545;
        background-color: #000;
    }
</style>

<?= CardWidget::widget([
    'title' => 'Información Acerca de los Entrenamientos',
    'content' => '
        <div class="row mb-3 align-items-center">
            <div class="col-5 text-end">
                ' . Html::img('@web/images/sistema/dinomenseñando1.png', [
        'alt' => 'Entrenamiento',
        'style' => 'max-width: 80px; height: auto; object-fit: cover;'
    ]) . '
            </div>
            <div class="col-7">
                ' . $htmlInstructores . '
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-12">
                <div class="container-fluid px-0">
                    ' . $htmlContent . '
                </div>
            </div>
        </div>',
    'footer' => false,
]) ?>