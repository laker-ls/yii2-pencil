<?php

namespace lakerLS\pencil\widgets;

use lakerLS\pencil\models\Image;
use lakerLS\pencil\PencilAsset;
use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
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
     * @var array обязательный параметр. Необходимо передать ширину и высоту для миниатюры, которую отображает виджет.
     * Необязательным параметром является `quality`, который задает качество в процентах, по умолчанию 50.
     *
     * ПРИМЕР:
     * PencilImage::begin(['group' => 'example', 'thumbnail' => ['width': 100, 'height': 50, 'quality': 70]]);
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
     * @var bool при значении `true` кнопка для создания/редактирования изображений не будет отображаться.
     * Актуально в случае, если необходимо вывести кнопку отдельно в другом месте.
     */
    public $hideButton = false;

    /**
     * @return string вывод ссылки на миниатюру изображения.
     */
    public function urlMini()
    {
        return '#{url-mini}';
    }

    /**
     * @return string вывод ссылки на оригинал изображения.
     */
    public function urlFull()
    {
        return '#{url-full}';
    }

    /**
     * @return string имя изображения.
     */
    public function alt()
    {
        return '#{alt}';
    }

    /**
     * @return string группа изображения.
     */
    public function group()
    {
        return '#{group}';
    }

    /**
     * @return string номер изображения. Начинается с 1.
     */
    public function index()
    {
        return '#{index}';
    }

    /**
     * Вывод кнопки в произвольном месте. Используется стандартный вид кнопки.
     * ПРИМЕР:
     *      <div>
     *          <?php $pencilImg = PencilImage::begin([...]); ?>
     *              <img ...>
     *          <?php PencilImage::end(); ?>
     *      </div>
     *      <?php $pencilImg->displayButton(); ?>
     */
    public function displayButton()
    {
        $this->defaultButton();
    }

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

        $images = new Image();
        $images = $images->findByGroup($this->group);

        echo Html::beginTag('div', ['data-target' => 'example-' . $this->group, 'style' => 'display: none;']);
            $result = $content;
            echo $result;
        echo Html::endTag('div');
        foreach ($images as $index => $image) {
            $result = strtr($content, [
                $this->urlMini() => $image->mini,
                $this->urlFull() => $image->full,
                $this->alt() => $image->alt,
                $this->group() => $image->group,
                $this->index() => $index + 1,
            ]);
            echo $result;
        }
        echo $this->button();
    }

    /**
     * Рендер кнопки. Имеется 2 варианта отображания, полноразмерное (для отображения коллекций изображений) и
     * небольшая кнопка в нижнем углу изображения (для отображения отдельных изображений).
     *
     * @throws \Exception
     */
    private function button()
    {
        if ($this->checkPermission() && $this->hideButton == false) {
            switch ($this->small) {
                case (false):
                    $this->defaultButton();
                    break;
                case (true):
                    $this->smallButton();
                    break;
                default:
                    throw new \Exception('Передано некорректное значение для свойства: small.');
            }
        }
    }

    /**
     * Формирование HTML кнопки для создания/редактирования изображений.
     * Данный вариант отображения используется по умолчанию.
     */
    private function defaultButton()
    {
        if ($this->checkPermission()) {
            $specificOptions = ['class' => 'big-gallery-button'];

            echo Html::beginTag('div', ['class' => 'pencil-gallery']);
                echo Html::a('Изменить изображения', '#', array_merge($this->generalOptionsButton(), $specificOptions));
            echo Html::endTag('div');
        }
    }

    /**
     * Формирование HTML кнопки для создания/редактирования изображений.
     * Данный вариант используется, если свойство `small` имеет значение `true`.
     */
    private function smallButton()
    {
        $specificOptions = ['class' => 'small-gallery-button'];

        echo Html::a('+', '#', array_merge($this->generalOptionsButton(), $specificOptions));
    }

    /**
     * Свойства для кнопки, которые используются всегда.
     *
     * @return array
     */
    private function generalOptionsButton()
    {
        return [
            'data-modal' => 'pencil-image',
            'data-group' => $this->group,
            'data-width' => $this->thumbnail['width'],
            'data-height' => $this->thumbnail['height'],
            'data-quality' => ArrayHelper::getValue($this->thumbnail, 'quality', 50),
        ];
    }
}