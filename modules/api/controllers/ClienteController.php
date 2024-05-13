<?php

namespace app\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use app\models\Orcamento;

class ClienteController extends ActiveController
{
//    public $modelClass = 'app\models\Orcamento';

    // public $modelClass = 'app\models\Orcamento';

    // public function actions()
    // {
    //     $actions = parent::actions();

    //     // Desabilitar as ações padrão de índice e visualização
    //     unset($actions['index'], $actions['view']);

    //     return $actions;
    // }

    // // Endpoint personalizado para retornar o orçamento com base no ID
    // public function actionOrcamentoPorId($id)
    // {
    //   $orcamentos = Orcamento::find()->where(['utilizador_id' => $id])->all();
    //     if ($orcamentos === null || count($orcamentos) === 0) {
    //         throw new \yii\web\NotFoundHttpException("O orçamento com o ID $id não foi encontrado.");
    //     }

    //     return $orcamentos;
    // }
    // public function actionrCreateOrcamento($id)
    // {
    //   $orcamentos = Orcamento::find()->where(['utilizador_id' => $id])->all();
    //     if ($orcamentos === null || count($orcamentos) === 0) {
    //         throw new \yii\web\NotFoundHttpException("O orçamento com o ID $id não foi encontrado.");
    //     }

    //     return $orcamentos;
    // }
}
