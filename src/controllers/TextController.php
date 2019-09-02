<?php

namespace lakerLS\pencil\controllers;

use lakerLS\pencil\models\Text;
use lakerLS\pencil\traits\AccessTrait;
use Yii;
use yii\db\Exception;
use yii\web\Controller;

/**
 * Контроллер для 'Text' модели.
 * Все экшены рассчитаны на работу через ajax.
 */
class TextController extends Controller
{
    use AccessTrait;

    protected function controllerName()
    {
        return 'text';
    }

    public $layout = false;

    /**
     * Отображение формы для редактирования/создания модели.
     * @param string $id
     * @param integer $category_id
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
     * @return string
     * @throws Exception
     */
    public function actionCreateUpdate()
    {
        $post = Yii::$app->request->post();
        $pencil = $post['Text'];

        $model = $this->findModel($pencil['id_name'], $pencil['category_id']);
        if ($model->load($post) && $model->save()) {
            Yii::$app->cache->flush();
            return $pencil['text'];
        } else {
            throw new Exception('Ошибка при записи в базу данных. Проверьте корректность данных POST.');
        }
    }

    /**
     * Поиск конкретной модели, если модель не существует, то создается объект для создания модели.
     *
     * @param string $id
     * @param null|integer $category_id
     * @return array|Text
     */
    private function findModel($id, $category_id = null)
    {
        $model = Text::findOne(['id_name' => $id]);
        if (empty($model) && !empty($category_id)) {
            $model = new Text();
            $model->id_name = $id;
            $model->category_id = $category_id;
        }
        return $model;
    }
}
