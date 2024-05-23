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
        unset($actions['view'],$actions['index'],$actions['update'],$actions['delete']);
        return $actions;
    }

    public function actionView($id)
    {
        // Obtém o modelo 'utilizador' pelo ID
        $utilizador = $this->findModel($id);

        // Verifica se o modelo 'utilizador' foi encontrado 
        if (!$utilizador) {
            throw new NotFoundHttpException('Utilizador não encontrado.');
        }

        // Obtém o cabeçalho de autorização da requisição
        $authorizationHeader = Yii::$app->getRequest()->getHeaders()->get('Authorization');

        // Busca o usuário pelo token de acesso
        $user = User::findByAccessToken($authorizationHeader);

        // Verifica se o usuário foi encontrado
        if (!$user) {
            throw new NotFoundHttpException('Utilizador não encontrado.');
        }

        // Verifica se o usuário autenticado tem permissão para visualizar o utilizador
        if ($utilizador->user_id !== $user->id) {
            throw new ForbiddenHttpException('Você não tem permissão para visualizar este utilizador.');
        }

        // Retorna os dados do usuário e do utilizador
        return [
            'utilizador' => $utilizador,
            'username' => $user -> username,
            'email' => $user -> email,
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
