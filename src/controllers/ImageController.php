<?php

namespace lakerLS\pencil\controllers;

use Exception;
use lakerLS\pencil\models\Image;
use lakerLS\pencil\traits\AccessTrait;
use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;

/**
 * Контроллер для `Image` модели.
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
     * @return string
     */
    public function actionIndex($group)
    {
        $newModel = new Image();
        $model = $newModel->findByGroup($group);

        return $this->render('index', [
            'model' => $model ? $model : $newModel,
            'group' => $group,
        ]);
    }

    /**
     * Данные принимаются без ajax запроса.
     * Добавление изображений в базу данных, а так же редактирование позиции существующих.
     * Позиция изображения определяется с помощью данных в массиве, связью ['имя изображения' => 'позиция'].
     *
     * @return \yii\web\Response
     * @throws Exception
     */
    public function actionCreateUpdate()
    {
        $post = Yii::$app->request->post();

        if (isset($post['Image'])) {
            $newImages = UploadedFile::getInstancesByName('Image[src]');
            foreach ($newImages as $image) {
                $model = new Image();
                $model->image = $image;

                $model->group = $post['Image']['group'];
                $model->src = $model->upload();
                $model->alt = $image->baseName;
                $model->position = $post['Position'][$image->name];
                if ($model->save()) {
                    Yii::$app->cache->flush();
                }
            }

            $imageModel = new Image();
            $activeRecords = $imageModel->findByGroup($post['Image']['group']);
            foreach ($activeRecords as $model) {
                $fullName = self::fullName($model);
                $model->position = $post['Position'][$fullName];
                if ($model->update()) {
                    Yii::$app->cache->flush();
                }
            }

            return $this->goBack();
        } else {
            throw new Exception('Данные методом POST небыли получены.');
        }
    }

    /** Удаление изображений из базы данных посредством ajax */
    public function actionDelete()
    {
        $id = Yii::$app->request->post('id');
        $id = mb_substr(mb_strstr($id, '-'), 1, mb_strlen($id));

        Image::findOne($id)->delete();
        Yii::$app->cache->flush();
    }

    /**
     * Получаем полное имя изображения с его расширением
     *
     * @param object $model
     * @return string
     */
    public static function fullName($model)
    {
        return $model->alt . mb_substr(mb_strstr($model->src, '.'), 0, strlen($model->src));
    }
}
