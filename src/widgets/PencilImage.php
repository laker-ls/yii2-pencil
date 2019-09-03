<?php

namespace lakerLS\pencil\widgets;

use lakerLS\pencil\PencilAsset;
use lakerLS\pencil\models\Image as ImageModel;
use yii\base\Widget;
use yii\bootstrap4\Html;
use lakerLS\pencil\traits\AccessWidgetTrait;

/**
 * Отображение изображений, которые видят все пользователи. Когда пользователь авторизован как администратор,
 * выводится кнопка в нужном вам месте для редактирование через модальное окно.
 *
 * В модальном окне изображения можно тасовать в необходимом вам порядке с помощью перетаскивания элементов мышью.
 * Заданный порядок будет сохранен и отображен соответствующе. Порядок может быть задан вне зависимости от того,
 * было ли изображение уже загружено или нет.
 *
 * Class PencilImage
 * @package lakerLS\pencil\widgets
 */
class PencilImage extends Widget
{
    use AccessWidgetTrait;

    /**
     * Обязательный параметр. Все изображения должны быть объеденены в альбомы.
     * @param string $group
     */
    public $group;

    /**
     * Подключение необходимых css и js.
     * Добавляем к $optionsAdmin обязательные атрибуты.
     */
    public function init()
    {
        parent::init();
        if ($this->checkPermission()) {
            PencilAsset::register($this->view);
        }
    }

    /**
     * Вывод кнопки для работы с изображениями в модальном окне.
     */
    public function run()
    {
        if ($this->checkPermission()) {
            echo Html::beginTag('div', ['class' => 'pencil-gallery']);
                echo Html::a('Изменить изображения', '#', [
                    'data-modal' => 'pencil-image',
                    'data-group' => $this->group,
                ]);
            echo Html::endTag('div');
        }
    }

    /**
     * Массив с выборкой изображений из базы данных для вывода в приложении.
     * Для вывода изображений используйте перебор циклом.
     *
     * Массив содержит следующие полезные параметры для отображения:
     * src - полный путь до изображения.
     * alt - наименование изображения.
     * group - наименование группы, в которую объеденены изображения.
     *
     * @param $params
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function arrayImg($params)
    {
        $model = new ImageModel();
        $model = $model->findByGroup($params['group']);

        return isset($model) ? $model : [];
    }
}