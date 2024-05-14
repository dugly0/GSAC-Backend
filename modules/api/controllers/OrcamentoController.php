<?php

namespace app\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use app\models\Orcamento;

class OrcamentoController extends BaseRestController
{
    public $modelClass = 'app\models\Orcamento';

    public function actions()
    {
        $actions = parent::actions();

        // Desabilitar as ações padrão de índice e visualização
        unset($actions['index'], $actions['view']);

        return $actions;
    }

    // Endpoint personalizado para retornar o orçamento com base no utilizador_id
    public function actionOrcamentoPorUtilizadorId($utilizador_id)
    {
        $orcamentos = Orcamento::find()->where(['utilizador_id' => $utilizador_id])->all();

        if (empty($orcamentos)) {
            throw new \yii\web\NotFoundHttpException("Não foram encontrados orçamentos para o utilizador com ID $utilizador_id.");
        }

        return $orcamentos;
    }
    public function actionCreate()
    {
        $requestData = Yii::$app->getRequest()->getBodyParams();
        
        // Verificar se os campos necessários estão presentes no corpo da requisição
        $requiredFields = ['data_entrada', 'descricao', 'utilizador_id'];
        foreach ($requiredFields as $field) {
            if (!isset($requestData[$field])) {
                throw new BadRequestHttpException("O campo '$field' é obrigatório.");
            }
        }

        // Criar um novo objeto de orçamento
        $model = new Orcamento();
        $model->load($requestData, '');

        // Salvar o novo orçamento
        if ($model->save()) {
            return $model;
        } else {
            throw new BadRequestHttpException("Falha ao criar o orçamento.");
        }
    }
}
