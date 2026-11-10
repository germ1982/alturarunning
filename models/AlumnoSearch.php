<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class AlumnoSearch extends Alumno
{
    public $nombre;
    public $documento;

    public function rules()
    {
        return [
            [['rol', 'estado'], 'integer'],
            [['nombre', 'documento'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function attributeLabels()
    {
        return parent::attributeLabels() + [
            'nombre' => 'Alumno',
            'documento' => 'Documento',
        ];
    }

    public function search($params)
    {
        $query = Alumno::find()->joinWith('persona')->with('pagos');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'nombre' => [
                        'asc' => ['persona.apellido' => SORT_ASC, 'persona.nombre' => SORT_ASC],
                        'desc' => ['persona.apellido' => SORT_DESC, 'persona.nombre' => SORT_DESC],
                    ],
                    'documento' => [
                        'asc' => ['persona.documento' => SORT_ASC],
                        'desc' => ['persona.documento' => SORT_DESC],
                    ],
                    'rol',
                    'estado',
                ],
                'defaultOrder' => ['nombre' => SORT_ASC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['rol' => $this->rol, 'estado' => $this->estado])
            ->andFilterWhere(['like', 'persona.documento', $this->documento]);

        foreach (preg_split('/\s+/', trim((string) $this->nombre), -1, PREG_SPLIT_NO_EMPTY) as $termino) {
            $query->andWhere(['or', ['like', 'persona.nombre', $termino], ['like', 'persona.apellido', $termino]]);
        }

        return $dataProvider;
    }
}
