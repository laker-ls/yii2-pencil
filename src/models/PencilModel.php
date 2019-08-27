<?php

namespace lakerLS\pencil\models;

use yii\db\ActiveRecord;

/**
 * Это модель для таблицы 'pencil'.
 *
 * @property string $id_name
 * @property integer $category_id
 * @property string $text
 */
class PencilModel extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pencil';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_name', 'category_id'], 'required'],
            [['category_id'], 'integer'],
            [['text'], 'string'],
            [['id_name'], 'string', 'max' => 60],
            [['id_name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_name' => 'Идентификатор',
            'category_id' => 'Родительская категория',
            'text' => 'Текст',
        ];
    }
}
