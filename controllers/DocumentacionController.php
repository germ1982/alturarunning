<?php

namespace app\controllers;

use app\helpers\ImagenHelper;
use app\helpers\UserHelper;
use app\models\Alumno;
use app\models\Persona;
use app\models\PersonaDocumentacion;
use app\models\SistemaLog;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\validators\FileValidator;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Documentación de una persona: la ven, suben y borran quienes gestionan alumnos
 * y la propia persona. Los archivos nunca se sirven directo desde web/.
 */
class DocumentacionController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [['allow' => true, 'roles' => ['@']]],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => ['subir' => ['POST'], 'eliminar' => ['POST']],
            ],
        ];
    }

    public function actionSubir($idalumno)
    {
        $alumno = Alumno::findOne((int) $idalumno);

        if ($alumno === null) {
            throw new NotFoundHttpException('El alumno no existe.');
        }

        $this->verificarAcceso($alumno->idpersona);

        $documento = new PersonaDocumentacion(['idpersona' => $alumno->idpersona]);
        $documento->load($this->request->post());
        $archivo = UploadedFile::getInstanceByName('archivo');
        $validador = new FileValidator([
            'skipOnEmpty' => false,
            'extensions' => PersonaDocumentacion::EXTENSIONES,
            'checkExtensionByMimeType' => true,
            'maxSize' => 10 * 1024 * 1024,
            'uploadRequired' => 'Elegí un archivo.',
        ]);

        if (!$documento->validate() || !$validador->validate($archivo, $error)) {
            $errores = $documento->getFirstErrors();
            Yii::$app->session->setFlash('error', $error ?? reset($errores));

            return $this->redirect(['alumno/ver', 'id' => $alumno->idalumno, '#' => 'documentacion']);
        }

        $carpeta = Yii::getAlias(PersonaDocumentacion::CARPETA);
        FileHelper::createDirectory($carpeta);
        $documento->archivo = Yii::$app->security->generateRandomString(32) . '.' . strtolower($archivo->extension);

        if (!$archivo->saveAs($documento->getRutaArchivo())) {
            throw new \RuntimeException('No se pudo guardar el archivo.');
        }

        // Las fotos de documentos se achican con más calidad para que se lean bien; los PDF quedan igual.
        ImagenHelper::optimizar($documento->getRutaArchivo(), ImagenHelper::ANCHO_DOCUMENTO, ImagenHelper::CALIDAD_DOCUMENTO);

        try {
            $documento->save(false);
        } catch (\Throwable $e) {
            @unlink($documento->getRutaArchivo());
            throw $e;
        }

        SistemaLog::registrar(SistemaLog::MODULO_DOCUMENTACION, SistemaLog::ACCION_CREATE, $documento->iddocumentacion, 'Se subió ' . $documento->tipo->descripcion . ' de ' . $alumno->persona->getNombreCompleto());
        Yii::$app->session->setFlash('success', 'La documentación se subió correctamente.');

        return $this->redirect(['alumno/ver', 'id' => $alumno->idalumno, '#' => 'documentacion']);
    }

    public function actionVer($id)
    {
        $documento = $this->buscarDocumento($id);
        $ruta = $documento->getRutaArchivo();

        if (!is_file($ruta)) {
            throw new NotFoundHttpException('El archivo no existe.');
        }

        // Queda registrado cada vez que alguien abre la documentación de otra persona.
        if ((int) (Yii::$app->user->identity->idpersona ?? 0) !== (int) $documento->idpersona) {
            $persona = Persona::findOne($documento->idpersona);
            SistemaLog::registrar(SistemaLog::MODULO_DOCUMENTACION, SistemaLog::ACCION_VER, $documento->iddocumentacion, 'Se abrió ' . $documento->tipo->descripcion . ' de ' . ($persona !== null ? $persona->getNombreCompleto() : 'la persona ' . $documento->idpersona));
        }

        $respuesta = Yii::$app->response;
        $respuesta->headers->set('X-Content-Type-Options', 'nosniff');
        $respuesta->headers->set('Cache-Control', 'private, no-store');

        return $respuesta->sendFile($ruta, $documento->tipo->descripcion . '.' . pathinfo($ruta, PATHINFO_EXTENSION), [
            'inline' => true,
            'mimeType' => FileHelper::getMimeType($ruta),
        ]);
    }

    public function actionEliminar($id)
    {
        $documento = $this->buscarDocumento($id);
        $alumno = Alumno::findOne(['idpersona' => $documento->idpersona]);
        $ruta = $documento->getRutaArchivo();

        $documento->delete();
        @unlink($ruta);

        SistemaLog::registrar(SistemaLog::MODULO_DOCUMENTACION, SistemaLog::ACCION_DELETE, (int) $id, 'Se eliminó ' . $documento->tipo->descripcion . ' de la persona ' . $documento->idpersona);
        Yii::$app->session->setFlash('success', 'La documentación se eliminó.');

        return $alumno !== null
            ? $this->redirect(['alumno/ver', 'id' => $alumno->idalumno, '#' => 'documentacion'])
            : $this->goHome();
    }

    private function buscarDocumento($id): PersonaDocumentacion
    {
        $documento = PersonaDocumentacion::findOne((int) $id);

        if ($documento === null) {
            throw new NotFoundHttpException('La documentación no existe.');
        }

        $this->verificarAcceso($documento->idpersona);

        return $documento;
    }

    private function verificarAcceso($idpersona): void
    {
        $propia = (int) (Yii::$app->user->identity->idpersona ?? 0) === (int) $idpersona;

        if (!$propia && !UserHelper::puede('alumnos')) {
            throw new ForbiddenHttpException('No tenés permiso para ver esta documentación.');
        }
    }
}
