<?php

namespace backend\forms;

use booking\entities\Order\Order;
use booking\entities\Slot\Slot;
use yii\base\Model;
use yii\data\ActiveDataProvider;


class OrderSearch extends Order
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
            [['id','customer_id', 'status','created_at','updated_at', 'author_id','editor_id'], 'integer'],
            [[ 'note'], 'safe'],
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
         *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Order::find();

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
            'status' => $this->status,
            'customer_id' => $this->customer_id,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'author_id' => $this->author_id,
            'editor_id' => $this->editor_id,
        ]);

        $query
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'author_name', $this->author_name])
            ->andFilterWhere(['like', 'editor_name', $this->editor_name]);

        return $dataProvider;
    }

}
