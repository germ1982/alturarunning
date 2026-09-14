<?php

namespace app\models;

use yii\base\Model;
use app\models\User;
use PHPUnit\Framework\Constraint\Count;
use Yii;

class UserSignupForm extends Model
{
      // Datos de la Persona
      public $nombre;
      public $apellido;
      public $documento;
      public $telefono;
      public $email;

      // Datos del Usuario
      public $username;
      public $password;
      public $password_repeat;
      public $status;

      public function rules()
      {
            return [
                  [['nombre', 'apellido', 'documento', 'username', 'email', 'password', 'password_repeat'], 'required'],

                  ['status', 'default', 'value' => 10],

                  // Validaciones de formato y unicidad en la tabla persona
                  ['email', 'email'],
                  ['email', 'unique', 'targetClass' => Persona::class, 'targetAttribute' => 'email', 'message' => 'Este email ya está registrado.'],
                  ['documento', 'unique', 'targetClass' => Persona::class, 'targetAttribute' => 'documento', 'message' => 'Este documento ya está registrado.'],

                  // Validaciones en la tabla user
                  ['username', 'unique', 'targetClass' => User::class, 'targetAttribute' => 'username', 'message' => 'Este usuario ya existe.'],

                  ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => 'Las contraseñas no coinciden.'],
                  ['password', 'string', 'min' => 6],

                  [['telefono'], 'string', 'max' => 30],
            ];
      }

      public function attributeLabels()
      {
            return [
                  'nombre' => 'Nombre',
                  'apellido' => 'Apellido',
                  'documento' => 'Documento',
                  'username' => 'Usuario',
                  'email' => 'Correo electrónico',
                  'telefono' => 'Teléfono',
                  'password' => 'Contraseña',
                  'password_repeat' => 'Confirmar Contraseña',
            ];
      }

      public function signup()
      {
            if (!$this->validate()) {
                  return false;
            }

            $transaction = Yii::$app->db->beginTransaction();


            try {
                  // 1. Crear y guardar la Persona primero
                  $persona = new Persona();
                  $persona->nombre = $this->nombre;
                  $persona->apellido = $this->apellido;
                  $persona->documento = $this->documento;
                  $persona->telefono = $this->telefono;
                  $persona->email = $this->email;

                  if (!$persona->save()) {
                        $transaction->rollBack();
                        return false;
                  }

                  $roles = User_rol::find()->all();

                  if (count($roles) == 0) {
                        // Si no hay roles, crear rol por defecto
                        $rol = new User_rol();
                        $rol->nombre = 'Administrador';
                        $rol->descripcion = 'Rol de administrador del sistema';
                        $rol->activo = 1;
                        $rol->save();
                        $idroladmin = $rol->idrol;

                        $rol = new User_rol();
                        $rol->nombre = 'Pendiente';
                        $rol->descripcion = 'Usuario a la espera de la asignacion de un rol funcional';
                        $rol->activo = 1;
                        $rol->save();
                        $idrolpendiente = $rol->idrol;

                        $rol = new User_rol();
                        $rol->nombre = 'Profesor';
                        $rol->descripcion = 'puede ver y editar todo el contenido';
                        $rol->activo = 1;
                        $rol->save();

                        $rol = new User_rol();
                        $rol->nombre = 'Alumno';
                        $rol->descripcion = 'puede crear y editar sus datos';
                        $rol->activo = 1;
                        $rol->save();

                        $rol = new User_rol();
                        $rol->nombre = 'Colaborador';
                        $rol->descripcion = 'Puede editar y eliminar contenido';
                        $rol->activo = 1;
                        $rol->save();

                        $rol = new User_rol();
                        $rol->nombre = 'Coordinador de Eventos';
                        $rol->descripcion = 'Puede publicar y editar tarjetas de eventos de los diferentes grupos de la comunidad';
                        $rol->activo = 1;
                        $rol->save();
                  } else {
                        $rol = User_rol::find()->where(['nombre' => 'Pendiente'])->one();
                        // Si por alguna razón no encuentra el rol 'Pendiente', lo creamos en el momento para evitar desastres
                        if (!$rol) {
                              $rol = new User_rol();
                              $rol->nombre = 'Pendiente';
                              $rol->descripcion = 'Usuario registrado sin rol definido en el sistema';
                              $rol->activo = 1;
                              $rol->save();
                        }
                        $idrolpendiente = $rol->idrol;
                  }

                  $usuarios = User::find()->all();
                  $idrol = count($usuarios) == 0 ? $idroladmin : $idrolpendiente;


                  // 3. Crear y guardar el Usuario vinculado a la Persona
                  $user = new User();
                  $user->username = $this->username;
                  $user->idpersona = $persona->idpersona; // Vinculación clave
                  $user->status = User::STATUS_ACTIVE;
                  $user->password_hash = Yii::$app->security->generatePasswordHash($this->password);
                  $user->auth_key = Yii::$app->security->generateRandomString();
                  $user->access_token = Yii::$app->security->generateRandomString();

                  if (!$user->save()) {
                        $transaction->rollBack();
                        return false;
                  }

                  // 4. Registrar Log
                  SistemaLog::registrar(
                        SistemaLog::MODULO_USUARIOS,
                        SistemaLog::ACCION_CREATE,
                        $user->id,
                        "Se creó el usuario " . $user->username
                  );
                  // 5. Asignar rol
                  $userRol = new User_usuario_rol();
                  $userRol->idusuario = $user->id;
                  $userRol->idrol = $idrol;
                  $userRol->save();

                  $transaction->commit();
                  return $user;
            } catch (\Exception $e) {
                  $transaction->rollBack();
                  throw $e;
            }


      }
}
