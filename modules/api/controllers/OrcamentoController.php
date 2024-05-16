<?php

namespace app\modules\api\controllers;

use Yii;

class OrcamentoController extends BaseRestController
{
    public $modelClass = 'app\models\Orcamento'; // Substitua com o caminho completo para o seu model

    /**
    * Obter todos os orçamentos de um determinado laboratório.
    *
    * @param int $laboratorioId ID do laboratório
    * @return array Lista de orçamentos
    */
    public function actionIndex($laboratorioId)
    {
        $laboratorioId = Yii::$app->request->get('laboratorioId');

        if (!$laboratorioId || !is_numeric($laboratorioId)) {
            throw new \yii\web\HttpException(400, 'Laboratorio ID inválido.');
        }

        $orcamentos = $this->modelClass::find()
            ->with('laboratorio')
            ->where(['laboratorio_id' => $laboratorioId])
            ->all();

        return $orcamentos;
    }

    /**
    * Obter um orçamento específico.
    *
    * @param int $id ID do orçamento
    * @return app\models\Orcamento Modelo do orçamento
    * @throws NotFoundHttpException Se o orçamento não for encontrado
    */
    public function actionView($id)
    {
        return $this->findModel($id)->with('laboratorio')->one(); // Eager loading do laboratório associado
    }

    // ... outras actions CRUD (actionCreate, actionUpdate e actionDelete)
}