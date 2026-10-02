<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

class UserSearch extends User
{
    // Declaramos las propiedades que ahora pertenecen a Persona
    public $email;
    public $telefono;

    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['username', 'email', 'telefono', 'access_token', 'auth_key'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params, $formName = null)
    {
        $query = User::find();

        // Unimos con la tabla persona para poder filtrar por sus campos
        $query->joinWith(['persona']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
        ]);

        // Filtramos usando la relación con persona
        $query->andFilterWhere(['like', 'user.username', $this->username])
            ->andFilterWhere(['like', 'persona.email', $this->email])
            ->andFilterWhere(['like', 'persona.telefono', $this->telefono]);

        return $dataProvider;
    }
}