<?php

namespace lakerLS\pencil\models;

use himiklab\thumbnail\EasyThumbnailImage;
use yii\db\ActiveRecord;
use Yii;

/**
 * Модель для таблицы "pencil_image".
 *
 * @property int $id
 * @property string $group
 * @property string $full
 * @property string $mini
 * @property string $alt
 * @property int $position
 * @property object $image В переменную передаются изображения для загрузки с компьютера.
 */
class Image extends ActiveRecord
{
    /**
     * Изображения для загрузки на сервер.
     * @var array $image
     */
    public $image;

    /**
     * Путь оригинального изображения.
     * @var string $fullPath
     */
    public $fullPath;

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
            [['group', 'full', 'position'], 'required'],
            [['full'], 'file', 'extensions' => 'png, jpg', 'maxFiles' => 20],
            [['position'], 'integer'],
            [['group', 'full', 'mini', 'alt'], 'string', 'max' => 255],
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
            'full' => 'Оригинал',
            'mini' => 'Миниатюра',
            'alt' => 'Наименование',
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

        return isset($model) ? $model : [];
    }

    /**
     * Загрузка полученных изображений на сервер.
     *
     * @return bool|string
     */
    public function uploadFull()
    {
        if (!empty($this->image)) {
            $nameImg = $this->uniqueName($this->image);
            $folder = substr($nameImg, 0, 2);
            $rootPath = Yii::$app->getModule('pencil')->params['imagePath']['full'];
            $pathImg = $rootPath . '/' . $folder;

            if (!is_dir($rootPath)) {
                mkdir($rootPath, 0777, true);
            }
            if (!is_dir($pathImg)) {
                mkdir($pathImg, 0777);
            }

            $this->fullPath = $pathImg . '/' . $nameImg;
            $this->image->saveAs($this->fullPath);
            
            return '/' . $this->fullPath;
        } else {
            return false;
        }
    }

    /**
     * Создание миниатюры изображения.
     *
     * @param $postImg array
     * @return string
     */
    public function uploadMini($postImg)
    {
        EasyThumbnailImage::$cacheAlias = Yii::$app->getModule('pencil')->params['imagePath']['mini'];

        return EasyThumbnailImage::thumbnailFileUrl(
            $this->fullPath,
            $postImg['width'],
            $postImg['height'],
            EasyThumbnailImage::THUMBNAIL_OUTBOUND,
            $postImg['quality']
        );
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

    /**
     * Полное имя изображения, с расширением.
     */
    public function fullName()
    {
        $name = mb_substr($this->alt, 0, 27);
        $extension = mb_strstr($this->full, '.');

        return $name . $extension;
    }
}
