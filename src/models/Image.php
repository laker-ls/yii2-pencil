<?php

namespace lakerLS\pencil\models;

use yii\db\ActiveRecord;
use Yii;

/**
 * Модель для таблицы "pencil_image".
 *
 * @property int $id
 * @property string $group
 * @property string $src
 * @property string $alt
 * @property int $position
 * @property object $image В переменную передаются изображения для загрузки с компьютера.
 */
class Image extends ActiveRecord
{
    public $image;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pencil_image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group', 'src', 'position'], 'required'],
            [['src'], 'file', 'extensions' => 'png, jpg', 'maxFiles' => 20],
            [['position'], 'integer'],
            [['group', 'src', 'alt'], 'string', 'max' => 255],
            [['alt'], function($attribute) {
                $group = self::findAll(['group' => $this->group]);
                foreach ($group as $img) {
                    if (isset($this->image->baseName) && $img->alt == $this->image->baseName) {
                        $this->addError($attribute, 'Совпадение имен в пределах группы');
                    }
                }
            }],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'group' => 'Группа',
            'src' => 'Src',
            'alt' => 'Alt',
            'position' => 'Позиция',
        ];
    }

    /** Выборка изображений по альбому.
     *
     * @param string $group
     * @return array|Image
     */
    public function findByGroup($group)
    {
        $model = self::find()
            ->where(['group' => $group])
            ->orderBy(['position' => SORT_ASC])
            ->cache()
            ->all();

        return $model;
    }

    /**
     * Загрузка полученных изображений на сервер.
     *
     * @return bool|string
     */
    public function upload()
    {
        if (!empty($this->image)) {
            $nameImg = $this->uniqueName($this->image);
            $folder = substr($nameImg, 0, 2);
            $path = Yii::$app->getModule('pencil')->params['imagePath'] . '/' . $folder;

            if (!is_dir($path)) {
                mkdir($path, 0777);
            }

            $this->image->saveAs($path . '/' . $nameImg);
            return '/' . $path . '/' . $nameImg;
        } else {
            return false;
        }
    }

    /**
     * Формирование имени файла с его расширением.
     *
     * @param object $image
     * @return string
     */
    private function uniqueName($image)
    {
        $random = time() + rand();
        $fullRandom = md5($random . $image->basename);
        $nameImg = $fullRandom . '.' . $image->extension;

        return $nameImg;
    }
}
