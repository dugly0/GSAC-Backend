<?php

namespace app\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use app\models\Orcamento;

class ClienteController extends BaseRestController
{
   public $modelClass = 'app\models\Orcamento';
    
}
