<?php

namespace app\modules\api\controllers;

use app\models\User;
use app\models\Utilizador;
use Yii;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class UserController extends BaseRestController{

    public $modelClass = User::class;

    public function behaviors()
   {
      $behaviors = parent::behaviors();

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
    
    public function actions()
    {
        $actions = parent::actions();

        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['index']);

        return $actions;
    }

    public function getUtilizador()
{
    return $this->hasOne(Utilizador::class, ['user_id' => 'id']);
}

public function actionIndex()
{
    $users = User::find()->with('utilizador')->all(); // Carrega os dados do Utilizador

    $data = [];
    foreach ($users as $user) {
        $userData = $user->toArray();
        $userData['utilizador'] = $user->utilizador ? $user->utilizador->toArray() : null; // Adiciona os dados do Utilizador
        $data[] = $userData;
    }

    return $data;
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

public function actionDelete($id)
{
    $transaction = Yii::$app->db->beginTransaction();

    try {
        // 1. Encontrar e excluir o utilizador na tabela Utilizador
        $utilizador = Utilizador::findOne(['user_id' => $id]);
        if (!$utilizador) {
            throw new NotFoundHttpException('Utilizador não encontrado.');
        }
        if (!$utilizador->delete()) {
            throw new ServerErrorHttpException('Erro ao excluir o utilizador.');
        }

        // 2. Encontrar e excluir o usuário na tabela User
        $user = $this->findModel($id);
        if (!$user->delete()) {
            throw new ServerErrorHttpException('Erro ao excluir o utilizador.');
        }

        $transaction->commit();

        // 3. Retorno RESTful (sem conteúdo, 204 No Content)
        Yii::$app->response->statusCode = 204;
        return;

    } catch (NotFoundHttpException $e) {
        // Mantenha o tratamento da NotFoundHttpException
        throw $e; 

    } catch (\Exception $e) {
        $transaction->rollBack();
        throw new ServerErrorHttpException('Erro no servidor ao excluir o utilizador.', 0, $e);
    }
}

public function actionView($id)
{
    $user = $this->findModel($id);

    if (!$user) {
        throw new NotFoundHttpException('Utilizador não encontrado.');
    }

    $utilizador = Utilizador::findOne(['user_id' => $id]);

    if (!$utilizador) {
        throw new NotFoundHttpException('Utilizador não encontrado.');
    }

    return [
        'user' => $user,
        'utilizador' => $utilizador,
    ];
}

public function findModel($id)
{
  $model = User::findOne($id);
  if (!$model) {
    throw new NotFoundHttpException('Utilizador não encontrado.');
  }
  return $model;
}

}