<?php

namespace lakerLS\pencil\widgets;

use lakerLS\pencil\models\Image;
use lakerLS\pencil\PencilAsset;
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
     * @var string обязательный параметр. Все изображения должны быть объеденены в альбомы.
     */
    public $group;

    /**
     * @var array передаем ширину и высоту для миниатюры, которую отображает виджет. Если параметр не указан,
     * изображение будет отображаться в пропорциях оригинала.
     *
     * ПРИМЕР:
     * PencilImage::begin(['group' => 'example', 'thumbnail' => ['width': 100, 'height': 50]]);
     */
    public $thumbnail;

    /**
     * @var boolean если значение `false`, отображается большая кнопка для редактирования.
     * Подходит для создания/редактирования альбомов.
     * Если значение `true`, кнопка имеет маленький размер и позицию absolute.
     * Подходит для создания/редактирования одного изображения, кнопка не будет "ломать" верстку.
     *
     * ВАЖНО: используя значение `true` обязательно указывайте родительскому элементу в котором расположено изображение
     * позицию `relative`.
     */
    public $small = false;

    /**
     * @var string для отображения одних и тех же изображений на нескольких страницах необходимо передать строку,
     * которая будет использоваться вместо `id`.
     */
    public $nonUnique;

    /**
     * @var Image Экземляр класса с изображениями.
     */
    private $model;

    /**
     * Инициализация виджета.
     */
    public function init()
    {
        parent::init();
        if (empty($this->group)) { // Выбрасываем исключение, если не переданы обязательные параметры.
            throw new \Exception('Не передан обязательный параметр: group.');
        }
        if ($this->checkPermission()) { // Подключаем css и js только для пользователя с правами на редактирование.
            PencilAsset::register($this->view);
        }
        if ($this->nonUnique === null) { // Создаем уникальное наименование группы для каждой страницы.
            $this->group = $this->view->context->categoryId . '-' . $this->group;
        } else {
            $this->group = $this->nonUnique . '-' . $this->group;
        }

        $this->model = new Image();
        $this->model = $this->model->findByGroup($this->group);

        ob_start();
    }

    /**
     * Отображение виджета.
     * Создается экземляр переданого html, который не отображается. Используется для отображения изменений через ajax.
     * В переданном html тег <img src="#"> заменяется на актуальное изображение.
     * Выводится кнопка редактирования для администратора.
     */
    public function run()
    {
        $content = ob_get_clean();

        echo Html::beginTag('div', ['data-target' => 'example-' . $this->group, 'style' => 'display: none;']);
            echo $content;
        echo Html::endTag('div');
        foreach ($this->model as $key => $model) {
            $img = Html::img($model->mini, ['alt' => $model->alt]);
            $replace = preg_replace('#<img.*src="(.*)".*>#isU', $img, $content);
            echo $replace;
        }
        echo $this->button();
    }

    /**
     * Рендер кнопки. Имеется 2 варианта отображания, полноразмерное (для отображения коллекций изображений) и
     * небольшая кнопка в нижнем углу изображения (для отображения отдельных изображений).
     *
     * @throws \Exception
     */
    public function button()
    {
        if ($this->checkPermission()) {
            $generalOptions = [
                'data-modal' => 'pencil-image',
                'data-group' => $this->group,
                'data-width' => $this->thumbnail['width'],
                'data-height' => $this->thumbnail['height'],
            ];

            if ($this->small === false) {
                $specificOptions = ['class' => 'big-gallery-button'];
                echo Html::beginTag('div', ['class' => 'pencil-gallery']);
                    echo Html::a('Изменить изображения', '#', array_merge($generalOptions, $specificOptions));
                echo Html::endTag('div');
            } elseif($this->small === true) {
                $specificOptions = ['class' => 'small-gallery-button'];
                echo Html::a('+', '#', array_merge($generalOptions, $specificOptions));
            } elseif ($this->small !== true && $this->small !== false) {
                throw new \Exception('Передано некорректное значение для свойства: small.');
            }
        }
    }
}