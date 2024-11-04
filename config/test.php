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
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'api/estadoorcamento',
                        'api/laboratorio',
                        'api/cliente',
                        'api/servico',
                        'api/servicoorcamento',
                        'api/utilizador',
                        'api/estado',
                        'api/user',
                        'api/orcamento',
                    ],
                    'extraPatterns' => [
                        'GET view-id' => 'view-id', // Rota personalizada para actionViewId
                        'PUT update-id' => 'update-id', // Rota personalizada para actionUpdateId
                    ],
                    'pluralize' => false
                ],
                // Rota personalizada para a atualização de orçamento
                'PUT api/orcamento/update/<id:\d+>' => 'api/orcamento/update',
                'PUT api/orcamento/update-orcamento-lab/<id:\d+>' => 'api/orcamento/update-orcamento-lab',
                'PUT api/orcamento/<orcamentoId:\d+>/servico-orcamento-lab/<servicoId:\d+>' => 'api/orcamento/update-servico-orcamento-lab',
                'POST api/orcamento/<orcamentoId:\d+>/create-estado-orcamento-lab' => 'api/orcamento/create-estado-orcamento-lab',
                'PUT api/user/forgot/<email:\d+>' => 'api/user/forgot',
                'DELETE api/orcamento/delete-servico-orcamento' => 'api/orcamento/delete-servico-orcamento',
                'PUT api/orcamento/update-estado' => 'api/orcamento/update-estado',

                

            ],
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
