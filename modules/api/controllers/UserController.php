<?php

namespace app\modules\api\controllers;

use amnah\yii2\user\models\UserToken;
use app\models\Role;
use app\models\User;
use app\models\Utilizador;
use Yii;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
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
               'actions' => ['index', 'create', 'update', 'delete', 'view', 'set-role', 'register'],
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

public function actionRegister()
{
    $user = new User();
    $post = $this->request->post();

    if (!$post) {
        throw new ServerErrorHttpException('Erro a criar utilizador');
    }

    // Carregar dados do usuário
    if ($user->load($post, '')) { 
        // Validar dados do usuário
        if ($user->validate()) {
            // Obter o role_id do POST (ou definir um padrão)
            $roleId = $post['role_id'] ?? Role::ROLE_USER; // Role::ROLE_USER é o padrão

            // Verificar se o roleId é válido
            if (!in_array($roleId, Role::getValidRoleIds())) {
                throw new BadRequestHttpException('Role inválido.');
            }

            $user->setRegisterAttributes($roleId); 

            if ($user->save()) {
                $utilizador = new Utilizador();
                $utilizador->load($post, '');
                $utilizador->user_id = $user->id;
                $utilizador->save();
            }

            $this->afterRegister($user);
        }
    }

    return $user;
}

protected function afterRegister($user)
    {
        /** @var \amnah\yii2\user\models\UserToken $userToken */
        $userToken = new UserToken();

        // determine userToken type to see if we need to send email
        $userTokenType = null;
        if ($user->status == $user::STATUS_INACTIVE) {
            $userTokenType = $userToken::TYPE_EMAIL_ACTIVATE;
        } elseif ($user->status == $user::STATUS_UNCONFIRMED_EMAIL) {
            $userTokenType = $userToken::TYPE_EMAIL_CHANGE;
        }

        // check if we have a userToken type to process, or just log user in directly
        if ($userTokenType) {
            $userToken = $userToken::generate($user->id, $userTokenType);
            if (!$numSent = $user->sendEmailConfirmation($userToken)) {

                // handle email error
                //Yii::$app->session->setFlash("Email-error", "Failed to send email");
            }
        } else {
            Yii::$app->user->login($user);
        }
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