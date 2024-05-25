<?php

namespace app\modules\api\controllers;

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

class AuthController extends Controller{

    public function behaviors()
    {
      $behaviors = parent::behaviors();

      unset($behaviors['authenticator']);

      $behaviors['corsFilter'] = [
         'class' => \yii\filters\Cors::class,
      ];
      return $behaviors;
    }

    public function actionLogin(){
        $model = new LoginForm();

        if($model->load($this->request->post(),'') &&
            $model->validate()){
                $user = $model->getUser();
                Yii::$app->user->login($user);
                return['access_token' =>
                Yii::$app->user->identity->access_token];
            }else{
                $model->validate();
                return $model;
            }
    }
    
    public function actionRegister(){
        $user = new User();
        // set scenario

        $post = $this->request->post();

        if(!$post){
            throw new ServerErrorHttpException('Erro a criar utilizador');
        }
        if($user->load($post, '')){
            if($user ->validate()){
                $role_id = Role::ROLE_USER;

                $user->setRegisterAttributes($role_id);
                $user->save();

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
}