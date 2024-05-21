<?php

namespace app\modules\api\controllers;

use app\models\User;
use app\models\Utilizador;
use Yii;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class UtilizadorController extends BaseRestController
{
   public $modelClass = 'app\models\Utilizador';

   public function behaviors()
   {
      $behaviors = parent::behaviors();

      $behaviors['access'] = [
         'class' => AccessControl::class,
         'rules' => [
             [
               'actions' => ['create', 'update', 'view'],
               'allow' => true,
               'roles' => ['@'],
            ],
         ]
      ];

      return $behaviors;
   }
   
   public function actions()
    {
        $actions = parent::actions();
        unset($actions['view']);
        unset($actions['index']);
        unset($actions['update']);
        return $actions;
    }

   public function actionView($id)
   {
   $utilizador = $this->findModel($id);

   if (!$utilizador) {
      throw new NotFoundHttpException('Utilizador não encontrado.');
   }

   $user = User::findOne($utilizador->user_id);

   if (!$user) {
      throw new NotFoundHttpException('Utilizador não encontrado.');
    }

   return [
      'user' => $user,
      'utilizador' => $utilizador,
   ];
   }

   public function actionUpdate($id)
    {
        // Encontra o Utilizador pelo ID
        $utilizador = $this->findModel($id);

        // Verifica se o Utilizador existe
        if (!$utilizador) {
            throw new NotFoundHttpException('Utilizador não encontrado.');
        }

        // Verifica se o Utilizador logado é o mesmo que está sendo atualizado
        if (Yii::$app->user->id != $utilizador->user_id) {
            throw new ForbiddenHttpException('Você não tem permissão para atualizar este utilizador.');
        }

        // Tenta carregar os dados da requisição no modelo Utilizador
        if ($utilizador->load(\Yii::$app->request->post(), '')) { // '' para carregar todos os atributos 
            if ($utilizador->save()) {
                return $utilizador; // Retorna o modelo atualizado com sucesso
            } else {
                // Em caso de erro na validação ou salvamento, retorna os erros
                \Yii::$app->response->statusCode = 422; // Unprocessable Entity
                return ['errors' => $utilizador->errors];
            }
        } else {
            // Em caso de erro ao carregar os dados da requisição
            \Yii::$app->response->statusCode = 400; // Bad Request
            return ['error' => 'Dados inválidos na requisição.'];
        }  
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