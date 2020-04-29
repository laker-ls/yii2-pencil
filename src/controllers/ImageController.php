<?php

namespace lakerLS\pencil\controllers;

use Exception;
use lakerLS\pencil\models\Image;
use lakerLS\pencil\models\query\ImageQuery;
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
            'width' => $width,
            'height' => $height,
        ]);
    }

    /**
     * Добавление изображений в базу данных, а так же редактирование позиции существующих.
     * Позиция изображения определяется с помощью данных в массиве связью ['имя изображения' => 'позиция'].
     *
     * @return string
     * @throws Exception
     */
    public function actionCreateUpdate()
    {
        $post = Yii::$app->request->post();

        if (isset($post['Image']) && isset($post['Position'])) {
            $newImages = UploadedFile::getInstancesByName('Image[full]');
            foreach ($newImages as $image) {
                $model = new Image();
                $model->image = $image;

                $model->group = $post['Image']['group'];
                $model->full = $model->uploadFull();
                $model->mini = $model->uploadMini($post['Image']);
                $model->alt = $image->baseName;
                $model->position = $post['Position'][$image->name];
                if ($model->save()) {
                    Yii::$app->cache->flush();
                } else {
                    $result['status'] = 'error';
                    $result['message'] = $model->errors[0];

                    return $result;
                }
            }

            $imageModel = new Image();
            $activeRecords = $imageModel->findByGroup($post['Image']['group']);
            /** @var Image $model */
            foreach ($activeRecords as $model) {
                $model->position = $post['Position'][$model->fullName()];
                if ($model->update()) {
                    Yii::$app->cache->flush();
                }
            }

            $result['status'] = 'success';
            $result['message'] = 'Изображения успешно загружены';
            $result['images'] = Image::find()
                ->where(['group' => $post['Image']['group']])
                ->orderBy(['position' => SORT_DESC])
                ->asArray()
                ->all();

            return json_encode($result);
        } else {
            $result['status'] = 'error';
            $result['message'] = 'Данные методом POST небыли получены. Обратитесь к разработчику.';

            return json_encode($result);
        }
    }

    /**
     * Удаление изображений из базы данных
     * @return string
     */
    public function actionDelete()
    {
        $post = Yii::$app->request->post();

        $id = $post['id'];
        $id = mb_substr(mb_strstr($id, '-'), 1, mb_strlen($id)); // удаление лишних данных из переданной строки.

        $image = Image::findOne($id);
        unlink(mb_substr($image->full, 1));
        unlink(mb_substr($image->mini, 1));
        if (!$image->delete()) {
            $result['status'] = 'error';
            $result['message'] = $image->errors[0];

            return json_encode($result);
        }

        Yii::$app->cache->flush();
        $result['status'] = 'success';
        $result['message'] = 'Изображение успешно удалено';
        $result['images'] = ImageQuery::findByGroupAsArray($post['group']);

        return json_encode($result);
    }

    /**
     * Удаление всех изображений конкретной группы.
     * @return integer|boolean
     */
    public function actionDeleteAll()
    {
        $post = Yii::$app->request->post();

        $images = Image::findAll(['group' => $post['group']]);
        foreach ($images as $image) {
            if (!$image->delete()) {
                Yii::$app->cache->flush();

                $result['status'] = 'error';
                $result['message'] = $image->errors[0];
                $result['images'] = ImageQuery::findByGroupAsArray($post['group']);

                return json_encode($result);
            };
        }
        Yii::$app->cache->flush();

        $result['status'] = 'success';
        $result['message'] = 'Все изображения успешно удалены';
        $result['images'] = null;

        return json_encode($images);
    }
}
