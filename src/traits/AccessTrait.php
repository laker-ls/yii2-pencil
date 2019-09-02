<?php

namespace lakerLS\pencil\traits;

use developeruz\db_rbac\behaviors\AccessBehavior;
use Yii;
use yii\db\Exception;

/** Методы для работы с правами доступа. */
trait AccessTrait
{
    /**
     * Доступ к Crud только для пользователей с указанной ролью.
     *
     * @return array
     */
    public function behaviors()
    {
        $roles = Yii::$app->getModule('pencil')->params['accessRoles'];

        return [
            'as AccessBehavior' => [
                'class' => AccessBehavior::class,
                'rules' => [
                    "pencil/{$this->controllerName()}" => [
                        [
                            'allow' => true,
                            'roles' => $this->getRoles($roles),
                        ],
                    ]
                ]
            ],
        ];
    }

    /**
     * Роль должна быть массивом, если приходит строка, конвертируем в массив, в остальных случаях ошибка.
     *
     * @param array|string $roles
     * @return array
     * @throws Exception
     */
    private function getRoles($roles)
    {
        if (is_array($roles)) {
            return $roles;
        } elseif (is_string($roles) && strrpos($roles, ',') === false) {
            return [$roles];
        } else {
            throw new Exception('Передаваемый параметр "accessRoles" должен быть массивом или строкой без запятых.');
        }
    }

    /** Необходимо передать имя текущего контроллера в нижнем регистре. */
    abstract protected function controllerName();
}