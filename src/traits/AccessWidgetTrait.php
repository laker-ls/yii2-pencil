<?php

namespace lakerLS\pencil\traits;

use Yii;

/** Методы для работы с отображением виджета в зависимости от прав доступа. */
trait AccessWidgetTrait
{
    /**
     * Проверка доступа роли.
     * @return boolean
     */
    private function checkPermission()
    {
        $roles = Yii::$app->getModule('pencil')->params['accessRoles'];
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