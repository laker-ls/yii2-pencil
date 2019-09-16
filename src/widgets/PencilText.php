<?php

namespace lakerLS\pencil\widgets;

use lakerLS\pencil\PencilAsset;
use lakerLS\pencil\models\Text as TextModel;
use lakerLS\pencil\traits\AccessWidgetTrait;
use yii\base\Widget;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;

/**
 * Отображение текста, которое редактируется через модальное окно, когда пользователь авторизован как администратор.
 * Не админ, видит обычный текст, в то же время администратор может взаимодействовать с ним, по нажатию на текст
 * всплывает модальное окно с одним полем textarea, переносы в данном поле работают и конвертируются в <br />, вся
 * стилизация текста должна задаваться через css.
 *
 * Вы можете настраивать в каком теге выводить текст и передавать ему классы и другие атрибуты.
 * Вид текста для администратора отличается и может быть дополнен атрибутами, которые выводят только для админа.
 *
 * В контроллере, в котором вызывается экшен с карандашами обязательно должно передаваться свойством id категории.
 *
 * ПРИМЕР:
 *      public $categoryId;
 *
 *      public function actionIndex()
 *      {
 *          $this->categoryId = 'example-index';         
 * 
 *          return $this->render('view');
 *      }
 *
 * Class PencilText
 * @package lakerLS\pencil\widgets
 */
class PencilText extends Widget
{
    use AccessWidgetTrait;

    /**
     * Обязательный параметр, для удобства, id указывать строкой. Необходимы уникальные имена в пределах одной страницы.
     * Повторное использование имен на других страницах не вызовет конфликта.
     * ПРИМЕР: ['id' => 'title-main']
     *
     * @var string $id
     */
    public $id;

    /**
     * Имя тега, в котором будет содержимое.
     * @var string $tag
     */
    public $tag = 'p';

    /**
     * Параметры тега, которые видны как всем пользователям, так и администратору.
     * @var array $options
     */
    public $options = [];

    /**
     * Для размещение текста в `layout` необходимо передать его имя.
     * @var string $layout
     */
    public $layout;

    /**
     * Параметры тега, которые видет только администратор. С помощью классов и стилей задается такой стиль текста,
     * что бы было понятно, что он интерактивен (можно редактировать). По умолчанию синее подчеркивание.
     *
     * @var array $optionsAdmin
     */
    public $optionsAdmin = ['class' => 'pencil-button'];

    /**
     * Текст, который виден только администратору, если содержимое пусто или не существует,
     * для возможности редактирования.
     *
     * @var string $textIsEmpty
     */
    public $textIsEmpty = 'Добавить текст';

    /**
     * Подключение необходимых css и js.
     * Формируем уникальный id из url и $id.
     * Добавляем к $optionsAdmin обязательные атрибуты.
     */
    public function init()
    {
        parent::init();

        if ($this->checkPermission()) {
            PencilAsset::register($this->view);
        }

        if (empty($this->layout)) {
            $currentCategory = $this->view->context->categoryId;
            $this->id = $currentCategory . '-' . $this->id;
        } else {
            $currentCategory = null;
            $this->id = $this->layout . '-' . $this->id;
        }

        $defaultAdmin = ['data-modal' => 'pencil-text', 'data-id' => $this->id, 'data-category' => $currentCategory];
        $this->optionsAdmin = array_merge($defaultAdmin, $this->optionsAdmin);
    }

    /**
     * Отображение текста. Если авторизован как 'admin', то текст становится кликабельным для редактирования.
     *
     * @return string|null
     */
    public function run()
    {
        $model = new TextModel();
        $model = $model->findModel($this->view->context->categoryId, $this->id);

        if (!empty($model->text)) {
            $lineBreak = str_replace(["\r\n", "\r", "\n"], '<br />', $model->text);
        }

        if ($this->checkPermission()) {
            $text = isset($lineBreak) ? $lineBreak : $this->textIsEmpty;
            return Html::tag($this->tag, $text, $this->glueArray($this->optionsAdmin, $this->options));
        } else {
            return isset($lineBreak) ? Html::tag($this->tag, $lineBreak, $this->options) : null;
        }
    }

    /**
     * Склеиваем параметры у элементов с одинаковым ключом.
     *
     * @param array $main Основные атрибуты.
     * @param array $additional дополнительные атрибуты.
     * @return array
     */
    private function glueArray($main, $additional)
    {
        $options = [];

        $mainKey = array_keys($main);
        $additionalKey = array_keys($additional);
        $sumArr = array_merge(array_flip($mainKey), array_flip($additionalKey));
        foreach ($sumArr as $key => $notNeed) {
            if (!empty(ArrayHelper::getValue($additional, $key))) {
                $options[$key] = ArrayHelper::getValue($main, $key) . ' ' . ArrayHelper::getValue($additional, $key);
            } else {
                $options[$key] = ArrayHelper::getValue($main, $key);
            }
        }
        return $options;
    }
}