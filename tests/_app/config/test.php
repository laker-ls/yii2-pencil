<?php

return [
    'id' => 'nested-set-menu-tests',
    'basePath' => dirname(__DIR__),
    //'bootstrap' => ['lakerLS\nestedSet\Bootstrap'],
    'language' => 'ru-RU',
    'aliases' => [
        '@laker-ls/nested-set-menu' => dirname(dirname(dirname(__DIR__))),
        '@tests' => dirname(dirname(__DIR__)),
        '@vendor' => VENDOR_DIR,
        '@bower' => VENDOR_DIR . '/bower-asset',
    ],
    'modules' => [
        'pencil' => [
            'class' => '\lakerLS\pencil\Module',
            'params' => [
                'accessRoles' => ['admin'],
                'imagePath' => [
                    'full' => 'upload/image-gallery/full',
                    'mini' => 'upload/image-gallery/mini',
                ],
            ],
        ],
    ],
    'components' => [
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['admin'],
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=yii2-pencil-test',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'useFileTransport' => true,
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
        ],
    ],
    'params' => [],
];