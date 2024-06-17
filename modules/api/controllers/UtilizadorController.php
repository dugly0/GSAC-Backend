<?php

namespace app\modules\api\controllers;

use app\models\User;
use app\models\Utilizador;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class UtilizadorController extends BaseRestController
{
    public $modelClass = 'app\models\Utilizador';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['view'],$actions['index'],$actions['update'],$actions['delete'], );
        return $actions;
    }

    public function actionViewId()
{
    // Obtém o cabeçalho de autorização
    $authorizationHeader = Yii::$app->getRequest()->getHeaders()->get('Authorization');

    // Busca o usuário pelo token de acesso
    $user = User::findByAccessToken($authorizationHeader);

    // Verifica se o usuário foi encontrado
    if (!$user) {
        throw new NotFoundHttpException('Usuário não encontrado.');
    }

    // Busca o Utilizador associado ao usuário
    $utilizador = Utilizador::findOne(['user_id' => $user->id]);

    // Verifica se o Utilizador foi encontrado
    if (!$utilizador) {
        throw new NotFoundHttpException('Utilizador não encontrado.');
    }

    // Retorna os dados do usuário e do utilizador
    return [
        'utilizador' => $utilizador,
        'username' => $user->username,
        'email' => $user->email,
        'password' => $user->password,
    ];
}

public function actionUpdateId()
{
    // Obtém o cabeçalho de autorização
    $authorizationHeader = Yii::$app->getRequest()->getHeaders()->get('Authorization');

    // Busca o usuário pelo token de acesso
    $user = User::findByAccessToken($authorizationHeader);

    // Verifica se o usuário foi encontrado
    if (!$user) {
        throw new NotFoundHttpException('Usuário não encontrado.');
    }

    // Obtém o ID do usuário que será atualizado
    $userId = Yii::$app->getRequest()->getBodyParam('id');

    // Verifica se o usuário logado é o mesmo que está sendo atualizado
    if ($user->id != $userId) {
        throw new ForbiddenHttpException('Você não tem permissão para atualizar este usuário.');
    }

    // Encontra o Utilizador pelo ID do usuário
    $utilizador = Utilizador::findOne(['user_id' => $userId]);

    // Verifica se o Utilizador foi encontrado
    if (!$utilizador) {
        throw new NotFoundHttpException('Utilizador não encontrado.');
    }

    // Carrega os dados recebidos na requisição para o modelo Utilizador
    if ($utilizador->load(Yii::$app->getRequest()->getBodyParams(), '') && $utilizador->save()) {
        return ['message' => 'Dados atualizados com sucesso.'];
    } else {
        Yii::$app->response->statusCode = 422; // Unprocessable Entity
        return ['errors' => $utilizador->errors];
    }
}

    public function actionView()
{
    // Obtém o cabeçalho de autorização
    $authorizationHeader = Yii::$app->getRequest()->getHeaders()->get('Authorization');

    // Busca o usuário pelo token de acesso
    $user = User::findByAccessToken($authorizationHeader);

    // Verifica se o usuário foi encontrado
    if (!$user) {
        throw new NotFoundHttpException('Usuário não encontrado.');
    }

    // Busca o Utilizador associado ao usuário
    $utilizador = Utilizador::findOne(['user_id' => $user->id]);

    // Verifica se o Utilizador foi encontrado
    if (!$utilizador) {
        throw new NotFoundHttpException('Utilizador não encontrado.');
    }

    // Retorna os dados do usuário e do utilizador
    return [
        'utilizador' => $utilizador,
        'username' => $user->username,
        'email' => $user->email,
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

        if ($utilizador->load(Yii::$app->request->post(), '')) {
            // Verifica se a senha precisa ser atualizada
            if (Yii::$app->request->post('password')) {
                // Encontra o usuário associado ao Utilizador
                $user = User::findOne($utilizador->user_id);
        
                if (!$user) {
                    throw new NotFoundHttpException('Usuário não encontrado.');
                }
        
                // Carrega o campo 'newPassword' do modelo User
                $user->load(['newPassword' => Yii::$app->request->post('password')], ''); 
        
                if (!$user->save()) {
                    Yii::$app->response->statusCode = 500;
                    return ['errors' => 'Erro ao atualizar a senha do usuário.'];
                }
            }
        
            // Salva o Utilizador 
            if ($utilizador->save()) {
                return ['message' => 'Informações atualizadas com sucesso'];
            } else {
                Yii::$app->response->statusCode = 422;
                return ['errors' => $utilizador->errors];
            }
        } else {
            Yii::$app->response->statusCode = 400;
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
