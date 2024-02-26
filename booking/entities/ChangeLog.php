<?php

namespace booking\entities;

use booking\entities\behaviors\FillingServiceFieldsBehavior;
use booking\entities\User\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "change_logs".
 *
 * @property int $id
 * @property string|null $model_class
 * @property int|null $model_id
 * @property string|null $attribute
 * @property string|null $value_old
 * @property string|null $value_new
 * @property int|null $date_time
 * @property string|null $model_json
 * @property int|null $action
 *
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $author_id
 * @property string|null $author_name
 * @property int|null $editor_id
 * @property string|null $editor_name
 *
 * @property User $author
 * @property User $editor
 */
class ChangeLog extends \yii\db\ActiveRecord
{
    const ACTION_INSERT=1;
    const ACTION_UPDATE=2;
    const ACTION_DELETE=3;

    public static function create(
        ?string $model_class,
        ?int $model_id,
        ?string $attribute,
        ?string $value_old,
        ?string $value_new,
        ?int $date_time,
        ?string $model_json,
        ?int $action

    ):self
    {
        $entity=new self();
        $entity->model_class=$model_class;
        $entity->model_id=$model_id;
        $entity->attribute=$attribute;
        $entity->value_old=$value_old;
        $entity->value_new=$value_new;
        $entity->date_time=$date_time;
        $entity->model_json=$model_json;
        $entity->action=$action;

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'change_logs';
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'model_class' => 'Model Class',
            'model_id' => 'Model ID',
            'attribute' => 'Attribute',
            'value_old' => 'Value Old',
            'value_new' => 'Value New',
            'date_time' => 'Date Time',
            'model_json' => 'Model Json',
            'action' => 'Action',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'author_id' => 'Author ID',
            'author_name' => 'Author Name',
            'editor_id' => 'Editor ID',
            'editor_name' => 'Editor Name',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            FillingServiceFieldsBehavior::class
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'author_id']);
    }

    /**
     * Gets query for [[Editor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEditor()
    {
        return $this->hasOne(User::class, ['id' => 'editor_id']);
    }
}
