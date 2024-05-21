<?php

namespace app\modules\api\controllers;

use amnah\yii2\user\models\User;
use app\models\Utilizador;
use yii\web\NotFoundHttpException;

class UtilizadorController extends BaseRestController
{
   public $modelClass = 'app\models\Utilizador';
   
   public function actions()
    {
        $actions = parent::actions();
        unset($actions['view']);
        unset($actions['index']);
        return $actions;
    }

   public function actionView($id)
{
    $utilizador = $this->findModel($id);

    if (!$utilizador) {
        throw new NotFoundHttpException('Utilizador não encontrado.');
    }

    $user = User::findOne(['id' => $id]);

    if (!$user) {
        throw new NotFoundHttpException('Utilizador não encontrado.');
    }

    return [
        'user' => $user,
        'utilizador' => $utilizador,
    ];
   }

   public function findModel($id)
   {
   $model = Utilizador::findOne($id);
   if (!$model) {
      throw new NotFoundHttpException('Utilizador não encontrado.');
   }
   return $model;
   }  
}
?>