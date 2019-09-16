<?php

namespace lakerLS\pencil\helpers;

/**
 * Различные функции для использования в пределах модуля.
 *
 * Class PencilHelper
 * @package lakerLS\pencil\helpers
 */
class PencilHelper
{
    /**
     * Полное имя изображения, с расширением.
     *
     * @param $model
     * @return string
     */
    public static function fullNameImg($model)
    {
        $name = $model->alt;
        $extension = mb_strstr($model->full, '.');

        return $name . $extension;
    }
}