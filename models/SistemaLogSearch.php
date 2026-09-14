<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * @property string|null $buscar
 * @property string|null $fecha_desde
 * @property string|null $fecha_hasta
 */
class SistemaLogSearch extends SistemaLog
{
    public $buscar;
    public $fecha_desde;
    public $fecha_hasta;

    public function rules()
    {
        return [
            [['buscar', 'fecha_desde', 'fecha_hasta'], 'safe'],
            [['idmodulo', 'idaccion', 'idusuario'], 'integer'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = SistemaLog::find()
            ->with(['usuario'])
            ->orderBy(['fecha' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => ['pageSize' => 20],
        ]);

        $this->load($params);
        if (!$this->validate()) return $dataProvider;

        if ($this->buscar) {
            $query->andFilterWhere(['like', 'descripcion', $this->buscar]);
        }

        if ($this->idmodulo !== null && $this->idmodulo !== '') {
            $query->andWhere(['sistema_log.idmodulo' => $this->idmodulo]);
        }

        if ($this->idaccion !== null && $this->idaccion !== '') {
            $query->andWhere(['sistema_log.idaccion' => $this->idaccion]);
        }

        if ($this->idusuario !== null && $this->idusuario !== '') {
            $query->andWhere(['sistema_log.idusuario' => $this->idusuario]);
        }

        if ($this->fecha_desde) {
            $query->andFilterWhere(['>=', 'sistema_log.fecha', $this->fecha_desde . ' 00:00:00']);
        }

        if ($this->fecha_hasta) {
            $query->andFilterWhere(['<=', 'sistema_log.fecha', $this->fecha_hasta . ' 23:59:59']);
        }

        return $dataProvider;
    }
}