<?php

namespace app\modules\api\controllers;


class OrcamentoController extends BaseRestController
{
   public $modelClass = 'app\models\Orcamento';
   namespace app\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\Response;

class OrcamentosController extends ActiveController
{
    public $modelClass = 'app\models\Orcamentos';

    // public function actions()
    // {
    //     $actions = parent::actions();
    //     unset($actions['index']); // Desativar a action padrão de listagem de orçamentos
    //     return $actions;
    // }

    // public function actionIndex()
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;

    //     // Sua consulta SQL para recuperar os orçamentos com informações dos estados
    //     $sql = "
    //         SELECT orcamento.*, orcamento AS nome_estado
    //         FROM orcamento
    //         LEFT JOIN estado ON orcamento.estado_id = estado.id
    //     ";

    //     $orcamentos = Yii::$app->db->createCommand($sql)->queryAll();

    //     return $orcamentos;
    }
}


?>