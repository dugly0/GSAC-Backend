<?php

namespace app\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use app\models\Orcamento;
use app\models\User;
use app\models\EstadoOrcamento;
use app\models\Estado;

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
        // // Obter o token da autorização dos cabeçalhos da solicitação
        $authorizationHeader = Yii::$app->getRequest()->getHeaders()->get('Authorization');
        $user = User::findByAccessToken($authorizationHeader);
        // //corta a string para obter apenas o token
        // $token = str_replace('Bearer ', '', $authorizationHeader);
        // //busca o utilizador com o token fornecido
        // $user = User::find()->where(['access_token' => $token])->one();
        if($utilizador_id == $user->id || $user->role_id == "1"){
            $orcamentos = Orcamento::find()->where(['utilizador_id' => $utilizador_id])->all();

            if (empty($orcamentos)) {
                throw new \yii\web\NotFoundHttpException("Não foram encontrados orçamentos para o utilizador com ID $utilizador_id.");
            }
            return $orcamentos;
        }
        else{
            throw new \yii\web\NotFoundHttpException("Voce não tem permissão para ver os orçamentos de outro utilizador.");
        }
    }
    public function actionCreate()
    {
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
    public function actionFindEstadoByIdOrcamento($idOrcamento){
        $idEstado = EstadoOrcamento::find()->where(['orcamento_id' => $idOrcamento])->one();
        if (empty($idEstado)) {
            throw new \yii\web\NotFoundHttpException("Não foram encontrados orçamentos para esse ID $idOrcamento.");
        }
        $estado = Estado::find()->where(['id' => $idEstado -> estado_id])->one();
        
        return  $estado->estado;
             
    }
    public function actionUpdateEstadoByIdOrcamento($idOrcamento, $idEstado)
{
    $estadoOrcamento = EstadoOrcamento::find()->where(['orcamento_id' => $idOrcamento])->one();

    if (empty($estadoOrcamento)) {
        throw new \yii\web\NotFoundHttpException("Não foram encontrados orçamentos para esse ID $idOrcamento.");
    }

    if ($idEstado != 1 && $idEstado != 2) {
        throw new \yii\web\BadRequestHttpException("O estado deve ser Aceito (1) ou Recusado (2).");
    }

    $estadoOrcamento->estado_id = $idEstado;
    $estadoOrcamento->data = date('Y-m-d'); // Formato apenas com dia, mês e ano

    if ($estadoOrcamento->save()) {
        $estado = Estado::find()->where(['id' => $idEstado])->one();
        return $estado->estado;
    } else {
        return [
            'success' => false,
            'message' => 'Falha ao atualizar o estado.',
            'errors' => $estadoOrcamento->errors,
        ];
    }
}

}
