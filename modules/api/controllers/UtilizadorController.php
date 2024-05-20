<?php

namespace app\modules\api\controllers;

use amnah\yii2\user\models\User;
use app\models\Utilizador;
use yii\filters\auth\HttpBearerAuth;
use yii\web\NotFoundHttpException;

class UtilizadorController extends BaseRestController
{
   public $modelClass = 'app\models\Utilizador';
}
?>