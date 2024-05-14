<?php

namespace app\modules\api\controllers;

use amnah\yii2\user\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class UserController extends ActiveController{

    public $modelClass = User::class;
    
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
               'actions' => ['index', 'create', 'update', 'delete', 'view', 'set-role'],
               'allow' => true,
               'roles' => ['admin'],
            ],
         ]
      ];

      return $behaviors;
    }
    public function actionSetRole($id, $role_id)
{
    $user = $this->findModel($id);
    if ($user && Yii::$app->user->can('admin')) {
        $user->role_id = $role_id;
        if ($user->save()) {
            return ['message' => 'Função alterada com sucesso'];
        } else {
            return ['errors' => $user->errors];
        }
    } else {
        throw new ForbiddenHttpException('Você não tem permissão para realizar essa ação.');
    }
}
public function findModel($id)
{
  $model = User::findOne($id);
  if (!$model) {
    throw new NotFoundHttpException('Usuário não encontrado.');
  }
  return $model;
}
}