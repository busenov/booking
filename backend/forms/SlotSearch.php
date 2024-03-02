<?php

namespace backend\forms;

use booking\entities\Slot\Slot;
use booking\helpers\AppHelper;
use booking\helpers\DateHelper;
use yii\base\Model;
use yii\data\ActiveDataProvider;


class SlotSearch extends Slot
{
    const PERIOD_MAX_DAY = 7;           //Выводить кнопки быстрой фильтрации на сколько дней вперед.
    public ?int $period=null;
    public function __construct($config = [])
    {
        $this->period=DateHelper::beginDay();
        parent::__construct($config);
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id','date','begin','end', 'qty', 'status','created_at','updated_at', 'author_id','editor_id','period','is_child','type'], 'integer'],
            ['period','in','range' => array_keys($this->getPeriodList())],
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
        $query = Slot::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50,
            ],
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
            'date' => $this->date,
            'begin' => $this->begin,
            'end' => $this->end,
            'is_child' => $this->is_child,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'author_id' => $this->author_id,
            'editor_id' => $this->editor_id,
        ]);

        $query
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'author_name', $this->author_name])
            ->andFilterWhere(['like', 'editor_name', $this->editor_name]);

        if ($this->period) {
            $query->andFilterWhere(['>=', 'date', DateHelper::beginDay($this->period)]);
            $query->andFilterWhere(['<=', 'date', DateHelper::endDay($this->period)]);
        }

        return $dataProvider;
    }
    public function getPeriodList()
    {
        $currentDay=DateHelper::beginDay();
        $period=[
            $currentDay=>'Текущий день ('.AppHelper::datetimeFormat($currentDay,false).')'
        ];
        for ($i=1;$i<self::PERIOD_MAX_DAY;$i++){
            $day=$currentDay+($i*(60*60*24));
            if ($i==1) {
                $name='Завтра ('.AppHelper::datetimeFormat($day,false).')';
            } elseif ($i==2) {
                $name='Послезавтра ('.AppHelper::datetimeFormat($day,false).')';
            } else {
                $name=AppHelper::datetimeFormat($day,false);
            }
            $period[$day] = $name;
        }
        return $period;
    }
}
