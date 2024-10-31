<?php

namespace app\modules\api\controllers;

use amnah\yii2\user\models\forms\ForgotForm;
use amnah\yii2\user\models\forms\LoginForm;
use amnah\yii2\user\models\Role;
use amnah\yii2\user\models\User;
use amnah\yii2\user\models\UserToken;
use app\models\Utilizador;
use Yii;
use yii\rest\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class AuthController extends Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['authenticator']);

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
        ];
        return $behaviors;
    }


    public function actionUpdatePassword()
    {
        $request = Yii::$app->request;
        $userId = $request->getBodyParam('id');
        $newPassword = $request->getBodyParam('password');

        try {
            $user = User::findOne($userId);
            if (!$user) {
                throw new NotFoundHttpException('Usuário não encontrado.');
            }

            // Encripta a nova senha antes de salvar no banco de dados
            $user->setPassword($newPassword);

            // Salva o modelo User com a nova senha encriptada
            if (!$user->save()) {
                throw new \Exception('Erro ao salvar nova senha.');
            }

            return ['message' => 'Senha atualizada com sucesso.'];
        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500; // Internal Server Error
            return ['error' => $e->getMessage()];
        }
    }

    public function actionLogin()
{
    $model = new LoginForm();

    if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
        $user = $model->getUser();

        // Verifica se o usuário existe e se a senha fornecida corresponde à senha armazenada
        if ($user && $user->validatePassword($model->password)) {
            // Autenticação bem-sucedida
            Yii::$app->user->login($user);

            return [
                'access_token' => Yii::$app->user->identity->access_token,
                'role_id' => Yii::$app->user->identity->role_id,
                'user_id' => Yii::$app->user->identity->id,
            ];
        } else {
            Yii::$app->response->statusCode = 401; // Unauthorized
            return ['error' => 'Credenciais inválidas.'];
        }
    } else {
        Yii::$app->response->statusCode = 422; // Unprocessable Entity
        return $model;
    }
}

    public function actionRegister()
    {
        $user = new User();
        // set scenario

        $post = $this->request->post();

        if (!$post) {
            throw new ServerErrorHttpException('Erro a criar utilizador');
        }
        if ($user->load($post, '')) {
            if ($user->validate()) {
                $role_id = Role::ROLE_USER;

                $user->setRegisterAttributes($role_id);
                $user->save();

                if ($user->save()) {
                    $utilizador = new Utilizador();
                    $utilizador->load($post, '');
                    $utilizador->user_id = $user->id;
                    $utilizador->save();
                }
                if(!$utilizador->save()){
                    $user->delete();
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
    public function actionForgot(){
        
        $model = new ForgotForm();

        if ($model->load(Yii::$app->request->post(), '') && $model->validate()) {
            if ($model->sendForgotEmail()) {
                return ['message' => 'Um email com instruções para recuperar a senha foi enviado.'];
            } else {
                throw new \yii\web\ServerErrorHttpException('Erro ao enviar o email de recuperação de senha.');
            }
        } else {
            return $model; // Retorna os erros de validação se houver
        }
    }
}
