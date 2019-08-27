<?php

namespace lakerLS\pencil\controllers;

use lakerLS\pencil\models\PencilModel;
use lakerLS\pencil\models\PencilSearch;
use Yii;
use yii\bootstrap4\Html;
use yii\web\Controller;

/**
 * Контроллер для 'pencil' модуля.
 * Все экшены рассчитаны на работу через ajax.
 */
class PencilController extends Controller
{
    public $layout = false;

    /**
     * Отображение формы для редактирования/создания модели.
     * @param string $id
     * @return string
     */
    public function actionIndex($id, $category_id)
    {
        $model = $this->findModel($id, $category_id);

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * Создание или редактирование модели.
     *
     * @param string $id
     * @return string
     */
    public function actionCreateUpdate()
    {
        $post = Yii::$app->request->post();
        $pencil = $post['PencilModel'];

        $model = $this->findModel($pencil['id_name'], $pencil['category_id']);
        if ($model->load($post) && $model->save()) {
            Yii::$app->cache->flush();

            return $pencil['text'];
        }
    }

    /**
     * Поиск конкретной модели, если модель не существует, то создается объект для создания модели.
     *
     * @param string $id
     * @param null|integer $category_id
     * @return ActiveRecord
     */
    private function findModel($id, $category_id = null)
    {
        $activeRecord = PencilModel::findOne(['id_name' => $id]);
        if (empty($activeRecord) && !empty($category_id)) {
            $activeRecord = new PencilModel();
            $activeRecord->id_name = $id;
            $activeRecord->category_id = $category_id;
        }

        return $activeRecord;
    }
}
