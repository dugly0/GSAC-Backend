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

      $behaviors['authentication'] = [
         'class' => CompositeAuth::class,
         'authMethods' => [
            HttpBearerAuth::class
         ]
      ];

      $behaviors['access'] = [
         'class' => AccessControl::class,
         'rules' => [
             [
                 'actions' => ['index', 'create', 'update', 'delete', 'view'],
                 'allow' => true,
                 'roles' => ['admin'],
            ],
            [
               'actions' => ['index', 'view'],
               'allow' => true,
               'roles' => ['laboratorio', '@'],
             ]
         ]
      ];

      return $behaviors;
   }


}