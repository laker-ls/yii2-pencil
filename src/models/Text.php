<?php

namespace lakerLS\pencil\models;

use yii\db\ActiveRecord;

/**
 * Модель для таблицы 'pencil_text'.
 *
 * @property string $id_name
 * @property integer $category_id
 * @property string $text
 */
class Text extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pencil_text';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_name'], 'required'],
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

    /**
     * Ищем необходимую модель для вывода изображений в виджете.
     * Производится запрос, которым получаются все записи для текущей страницы, после чего кэшируется.
     *
     * @param integer $categoryId
     * @param string $idName
     * @return array|ActiveRecord
     */
    public function findModel($idName, $categoryId = null)
    {
        $records = self::find()
            ->where(['category_id' => $categoryId])
            ->orWhere(['category_id' => null])
            ->cache()
            ->all();

        /** @var self $record */
        foreach ($records as $record) {
            if ($record->id_name == $idName) {
                $result = $record;
            }
        }
        return isset($result) ? $result : [];
    }
}
