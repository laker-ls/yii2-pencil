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
     * @var string $group
     */
    public $group;

    /**
     * Необязательный параметр.
     * Если значение `false`, отображается большая кнопка для редактирования. Подходит для создания/редактирования
     * альбомов.
     * Если значение `true`, кнопка имеет маленький размер и позицию absolute. Подходит для создания/редактирования
     * одного изображения, кнопка не будет "ломать" верстку.
     *
     * ВАЖНО: используя значение `true` обязательно указывайте родительскому элементу в котором расположено изображение
     * позицию `relative`.
     *
     * @var boolean $small
     */
    public $small = false;

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
            if ($this->small === false) {
                echo Html::beginTag('div', ['class' => 'pencil-gallery']);
                echo Html::a('Изменить изображения', '#', [
                    'data-modal' => 'pencil-image',
                    'data-group' => $this->group,
                    'class' => 'big-gallery-button'
                ]);
                echo Html::endTag('div');
            } elseif($this->small === true) {
                echo Html::a('+', '#', [
                    'data-modal' => 'pencil-image',
                    'data-group' => $this->group,
                    'class' => 'small-gallery-button'
                ]);
            } else {
                throw new \Exception('Передано некорректное значение для свойства "small"');
            }

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