<?php

namespace lakerLS\pencil\controllers;

use Exception;
use lakerLS\pencil\models\Image;
use lakerLS\pencil\traits\AccessTrait;
use lakerLS\pencil\helpers\PencilHelper;
use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;

/**
 * Контроллер для `Image` модели. Все action п
 */
class ImageController extends Controller
{
    use AccessTrait;

    protected function controllerName()
    {
        return 'image';
    }

    public $layout = false;

    /**
     * Отображение формы для загрузки изображений (модальное окно).
     *
     * @param string $group
     * @param string $width миниатюры
     * @param string $height миниатюры
     * @return string
     */
    public function actionIndex($group, $width, $height)
    {
        $newModel = new Image();
        $model = $newModel->findByGroup($group);

        return $this->render('index', [
            'model' => !empty($model) ? $model : $newModel,
            'group' => $group,
            'width' => $width,
            'height' => $height,
        ]);
    }

    /**
     * Добавление изображений в базу данных, а так же редактирование позиции существующих.
     * Позиция изображения определяется с помощью данных в массиве связью ['имя изображения' => 'позиция'].
     *
     * @return \yii\web\Response
     * @throws Exception
     */
    public function actionCreateUpdate()
    {
        $post = Yii::$app->request->post();
        $width = $post['Image']['width'];
        $height = $post['Image']['height'];

        if (isset($post['Image'])) {
            $newImages = UploadedFile::getInstancesByName('Image[full]');
            foreach ($newImages as $image) {
                $model = new Image();
                $model->image = $image;

                $model->group = $post['Image']['group'];
                $model->full = $model->uploadFull();
                $model->mini = $model->uploadMini($width, $height);
                $model->alt = $image->baseName;
                $model->position = $post['Position'][$image->name];
                if ($model->save()) {
                    Yii::$app->cache->flush();
                } else {
                    var_dump($model->errors);
                }
            }

            $imageModel = new Image();
            $activeRecords = $imageModel->findByGroup($post['Image']['group']);
            foreach ($activeRecords as $model) {
                $fullName = PencilHelper::fullNameImg($model);
                $model->position = $post['Position'][$fullName];
                if ($model->update()) {
                    Yii::$app->cache->flush();
                }
            }
            $result = Image::find()
                ->where(['group' => $post['Image']['group']])
                ->orderBy(['position' => SORT_DESC])
                ->asArray()
                ->all();

            return json_encode($result);
        } else {
            throw new Exception('Данные методом POST небыли получены.');
        }
    }

    /**
     * Удаление изображений из базы данных
     * @return string
     */
    public function actionDelete()
    {
        $this->enableCsrfValidation = false;
        $post = Yii::$app->request->post();

        echo '<pre>'; print_r(1); echo '</pre>'; die;

        $id = $post['id'];
        $id = mb_substr(mb_strstr($id, '-'), 1, mb_strlen($id)); // удаление лишних данных из переданной строки.

        $image = Image::findOne($id);
        unlink(mb_substr($image->full, 1));
        unlink(mb_substr($image->mini, 1));
        $image->delete();
        Yii::$app->cache->flush();

        $result = Image::find()->where(['group' => $post['group']])->asArray()->orderBy(['position' => SORT_DESC])->all();
        return json_encode($result);
    }
}
