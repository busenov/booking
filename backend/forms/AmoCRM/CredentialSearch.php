<?php

namespace backend\forms\AmoCRM;

use booking\entities\AmoCRM\Credential;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 */
class CredentialSearch extends Credential
{

    public function __construct($config = [])
    {
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','expires'], 'integer'],
            [['domain', 'token', 'refresh_token', 'widget_client_id', 'client_secret', 'redirect_uri'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Выводим компании только где пользователь администратор, или где она включен как пользователь
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Credential::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'expires' => $this->expires,
        ]);

        $query->andFilterWhere(['like', 'domain', $this->domain])
            ->andFilterWhere(['like', 'token', $this->token])
            ->andFilterWhere(['like', 'refresh_token', $this->refresh_token])
            ->andFilterWhere(['like', 'client_id', $this->client_id])
            ->andFilterWhere(['like', 'client_secret', $this->client_secret])
            ->andFilterWhere(['like', 'redirect_uri', $this->redirect_uri]);
        return $dataProvider;
    }

}
