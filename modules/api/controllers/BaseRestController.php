<?php

namespace app\modules\api\controllers;

use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;

class BaseRestController extends ActiveController{

   public function behaviors()
   {
      $behaviors = parent::behaviors();

      unset($behaviors['authenticator']);

      $behaviors['corsFilter'] = [
         'class' => \yii\filters\Cors::class,
      ];

      $behaviors['authentication'] = [
         'class' => CompositeAuth::class,
         'authMethods' => [
            HttpBearerAuth::class
         ]
      ];

      $behaviors['access'] = [
         'class' => AccessControl::class,
         'rules' => [ //add autorizações ao admin
             [
               'actions' => ['index', 'create', 'update', 'delete', 'view', 'set-role', 'view-id', 'update-id','delete-servico-orcamento'],
               'allow' => true,
               'roles' => ['admin'],
            ],
            [
               'actions' => ['index', 'view', 'view-id', 'update-id', 'create','update', 'orcamento-por-utilizador-id', 'find-estado-by-id-orcamento', 'update-estado-by-id-orcamento', 'find-servico-by-id-orcamento', 'forgot','delete-servico-orcamento', 'update-estado'
            ],
               'allow' => true,
               'roles' => ['@'],
            ],
            [
               'actions' => ['index', 'view', 'view-id', 'update-id', 'update', 'create', 'orcamento-por-laboratorio', 'orcamento-por-laboratorio-com-estado-aceito', 'update-orcamento-lab', 'update-servico-orcamento-lab', 'create-estado-orcamento-lab'
            ],
               'allow' => true,
               'roles' => ['lab'],
            ],
         ]
      ];

      return $behaviors;
   }


}