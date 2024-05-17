<?php

namespace app\modules\api\controllers;

use yii\filters\auth\HttpBearerAuth;

class UtilizadorController extends BaseRestController
{
   public $modelClass = 'app\models\Utilizador';

   public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
        ];
        return $behaviors;
    }

}

?>