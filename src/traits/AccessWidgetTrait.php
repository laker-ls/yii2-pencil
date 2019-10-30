<?php

namespace lakerLS\pencil\traits;

use Yii;
use yii\helpers\ArrayHelper;

/** Методы для работы с отображением виджета в зависимости от прав доступа. */
trait AccessWidgetTrait
{
    /**
     * Проверка доступа роли.
     * @return boolean
     */
    private function checkPermission()
    {
        $roles = ArrayHelper::getValue(Yii::$app->getModule('pencil')->params, 'accessRoles', ['admin']);

        if (is_string($roles)) {
            $roles = [$roles];
        }

        foreach ($roles as $role) {
            if (Yii::$app->user->can($role)) {
                return true;
            }
        }
        return false;
    }
}