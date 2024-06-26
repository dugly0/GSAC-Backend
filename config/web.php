<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'cookieValidationKey' => 'wRLkQeGZcKE_hykq9L_4i_QTQ7hSJlId',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'class' => 'amnah\yii2\user\components\User',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'transport' => [
                'scheme' => 'smtps',
                'host' => 'smtp.gmail.com',
                'username' => 'ipborcamentos@gmail.com',
                'password' => 'wgab meud kuio ypgq',
                'port' => 465,
            ],
            // 'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'messageConfig' => [
                'from' => ['ipborcamentos@gmail.com' => 'IPB.Orçamentos'],
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,

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

    ],
    'modules' => [
        'user' => [
            'class' => 'amnah\yii2\user\Module',
            'requireEmail' => true,
            'requireUsername' => true
        ],
        'api' => [
            'class' => 'app\modules\api\RestApi'
        ]
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
