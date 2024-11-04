<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/test_db.php';

return [
    'id' => 'basic-tests',
    'basePath' => dirname(__DIR__),
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'language' => 'en-US',
    'components' => [
        'db' => $db,
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'useFileTransport' => true,
            'viewPath' => '@app/mail',
        ],
        'user' => [
            'class' => 'amnah\yii2\user\components\User',
            'identityClass' => 'amnah\yii2\user\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => null, // Evitar redirecionamento para página de login nos testes
        ],
        'assetManager' => [
            'basePath' => __DIR__ . '/../web/assets',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => true,
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
        ],
    ],
    'modules' => [
        'user' => [
            'class' => 'amnah\yii2\user\Module',
            'requireEmail' => true,
            'requireUsername' => true,
        ],
        'api' => [
            'class' => 'app\modules\api\RestApi',
        ],
    ],
    'params' => $params,
];
