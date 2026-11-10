<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class EventoSearch extends Evento
{
    public function rules()
    {
        return [
            [['estado'], 'integer'],
            [['titulo', 'fecha_evento'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params)
    {
        $query = Evento::find()->with('desafios.inscripciones');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['fecha_evento' => SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['estado' => $this->estado])
            ->andFilterWhere(['like', 'titulo', $this->titulo])
            ->andFilterWhere(['like', 'fecha_evento', $this->fecha_evento]);

        return $dataProvider;
    }
}
