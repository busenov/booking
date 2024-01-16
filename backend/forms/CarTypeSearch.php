<?php

namespace backend\forms;

use booking\repositories\UserRepository;
use booking\entities\Car\CarType;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * TeamSearch represents the model behind the search form of `app\models\Teams`.
 */
class CarTypeSearch extends CarType
{

    private UserRepository $userRepository;
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->userRepository = new UserRepository();
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'qty', 'status','created_at','updated_at', 'author_id','editor_id'], 'integer'],
            [['name', 'description', 'note'], 'safe'],
            [['pwr', ], 'double'],
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
        $query = CarType::find();

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
            'qty' => $this->qty,
            'pwr' => $this->pwr,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'author_id' => $this->author_id,
            'editor_id' => $this->editor_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'author_name', $this->author_name])
            ->andFilterWhere(['like', 'editor_name', $this->editor_name]);

        return $dataProvider;
    }

}
