<?php

namespace app\modules\api\controllers;

use Yii;
use yii\rest\ActiveController;
use app\models\Orcamento;
use app\models\User;
use app\models\EstadoOrcamento;
use app\models\Estado;
use app\models\Servico;
use app\models\ServicoOrcamento;
use app\models\Utilizador;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\base\Exception;

class OrcamentoController extends BaseRestController
{
    public $modelClass = 'app\models\Orcamento';
    public function actions()
    {
        $actions = parent::actions();

        // Desabilitar as ações padrão de índice e visualização
        unset($actions['index'], $actions['view'], $actions['create']);

        return $actions;
    }
    // Endpoint personalizado para retornar o orçamento com base no utilizador_id
    public function actionOrcamentoPorUtilizadorId()
    {
        // Obter o token da autorização dos cabeçalhos da solicitação
        $authorizationHeader = Yii::$app->getRequest()->getHeaders()->get('Authorization');
        // Encontrar o user correspondente ao usuário autenticado
        $user = User::findByAccessToken($authorizationHeader);
        if ($user->role_id == 1) {
            $orcamentos = Orcamento::find()->all();
            //retornar todos os orçamentos se user for admin
            return $orcamentos;
        }
        // Buscar os orçamentos do utilizador
        $utilizador = Utilizador::find()->where(['user_id' => $user->id])->one();
        //trazendo os orçamentos do utilizador      
        $orcamentos = Orcamento::find()
            ->where(['utilizador_id' => $utilizador->id])
            ->with([
                'servicos' => function ($query) {
                    $query->innerJoin('servico_orcamento', 'servico.id = servico_orcamento.servico_id')
                        ->select(['servico.*','servico_orcamento.*']);
                },
                'estados' => function ($query) {
                    $query->innerJoin('estado_orcamento', 'estado.id = estado_orcamento.estado_id')
                        ->select(['estado.*']);
                }
            ])
            ->asArray()
            ->all();

        if (empty($orcamentos)) {
            throw new \yii\web\NotFoundHttpException("Não foram encontrados orçamentos para o utilizador com ID $utilizador->id.");
        }
        return $orcamentos;
    }   
    public function actionCreate()
    {                
        // Obter o token da autorização dos cabeçalhos da solicitação
        $authorizationHeader = Yii::$app->getRequest()->getHeaders()->get('Authorization');
        // Encontrar o user correspondente ao usuário autenticado
        $user = User::findByAccessToken($authorizationHeader);
        // Buscar o utilizador correspondente ao token
        $utilizador = Utilizador::find()->where(['user_id' => $user->id])->one();
        // Pegando os dados do corpo da requisição
        $post = $this->request->post();
        // Verificar se os campos necessários estão presentes no corpo da requisição
        if (!isset($post['descricao'])) {
            throw new BadRequestHttpException("Faltam campos obrigatórios.");
        }
        // Criar um novo objeto de orçamento
        $model = new Orcamento();
        $model->data_entrada = date('Y-m-d');
        $model->utilizador_id = $utilizador->id;
        $model->load($post, '');
        // Salvar o novo orçamento
        if ($model->save()) {
            // Criar um novo objeto de estado do orçamento
            $estadoOrcamento = new EstadoOrcamento();
            $estadoOrcamento->orcamento_id = $model->id;
            $estadoOrcamento->estado_id = 1;
            $estadoOrcamento->data = date('Y-m-d'); // Formato apenas com dia, mês e ano
            $estadoOrcamento->save();
            return $model;
        } else {
            throw new BadRequestHttpException("Falha ao criar o orçamento.");
        }
    }
    public function actionFindEstadoByIdOrcamento($idOrcamento)
    {
        // Obter o token da autorização dos cabeçalhos da solicitação
        $authorizationHeader = Yii::$app->getRequest()->getHeaders()->get('Authorization');
        // Encontrar o user correspondente ao usuário autenticado
        $user = User::findByAccessToken($authorizationHeader);

        if ($user->role_id == 1) {
            // Retornar o orçamento especificado se o usuário for admin
            $orcamento = Orcamento::find()
                ->where(['id' => $idOrcamento])
                ->with([
                    'servicos' => function ($query) {
                        $query->innerJoin('servico_orcamento', 'servico.id = servico_orcamento.servico_id')
                            ->select(['servico.*', 'servico_orcamento.*']);
                    },
                    'estados' => function ($query) {
                        $query->innerJoin('estado_orcamento', 'estado.id = estado_orcamento.estado_id')
                            ->select(['estado.estado', 'estado_orcamento.data']);
                    }
                ])
                ->asArray()
                ->one();

            if (empty($orcamento)) {
                throw new \yii\web\NotFoundHttpException("Não foi encontrado orçamento com ID $idOrcamento.");
            }

            return $orcamento;
        }

        // Buscar o utilizador correspondente ao usuário autenticado
        $utilizador = Utilizador::find()->where(['user_id' => $user->id])->one();

        
        // Buscar o orçamento específico do utilizador
        $orcamento = Orcamento::find()
            ->where(['utilizador_id' => $utilizador->id, 'id' => $idOrcamento])
            ->with([
                'servicos' => function ($query) {
                    $query->innerJoin('servico_orcamento', 'servico.id = servico_orcamento.servico_id')
                        ->select(['servico.*', 'servico_orcamento.*']);
                },
                'estados' => function ($query) {
                    $query->innerJoin('estado_orcamento', 'estado.id = estado_orcamento.estado_id')
                        ->select(['estado.*', 'estado_orcamento.data']);
                }
            ])
            ->asArray()
            ->one();

        if (empty($orcamento)) {
            throw new \yii\web\NotFoundHttpException("Não foi encontrado orçamento com ID $idOrcamento para o utilizador com ID $utilizador->id ou o orçamento não foi criado pelo usuário.");
        }

        return $orcamento;
    }
    public function actionUpdateEstadoByIdOrcamento($idOrcamento, $idEstado)
    {
        // Obter o token da autorização dos cabeçalhos da solicitação
        $authorizationHeader = Yii::$app->getRequest()->getHeaders()->get('Authorization');
        // Encontrar o user correspondente ao usuário autenticado
        $user = User::findByAccessToken($authorizationHeader);
        // Buscar os dados do utilizador
        $utilizador = Utilizador::find()->where(['user_id' => $user->id])->one();
       
        // Verificar se o orçamento existe
        $orcamento = Orcamento::find()->where(['id' => $idOrcamento])->one();
        if (empty($orcamento)) {
            throw new \yii\web\NotFoundHttpException("Não foram encontrados orçamentos para esse ID $idOrcamento.");
        }
        if($user->role_id == 1 || $orcamento->utilizador_id == $utilizador->id){
            // Verificar se o estado é Aceito (1) ou Recusado (2)
            if ($idEstado != 1 && $idEstado != 2) {
                throw new \yii\web\BadRequestHttpException("O estado deve ser Aceito (1) ou Recusado (2).");
            }

            // Criar uma nova instância de EstadoOrcamento
            $estadoOrcamento = new EstadoOrcamento();
            $estadoOrcamento->orcamento_id = $idOrcamento;
            $estadoOrcamento->estado_id = $idEstado;
            $estadoOrcamento->data = date('Y-m-d'); // Formato apenas com dia, mês e ano

            // Salvar o novo estado do orçamento
            if ($estadoOrcamento->save()) {
                $estado = Estado::find()->where(['id' => $idEstado])->one();
                return [
                    'success' => true,
                    'message' => 'Estado do orçamento salvo com sucesso.',
                    'estado' => $estado->estado,
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Falha ao salvar o estado.',
                    'errors' => $estadoOrcamento->errors,
                ];
            }
        }
        throw new \yii\web\NotFoundHttpException("Não foi encontrado orçamento com ID $idOrcamento para o utilizador com ID $utilizador->id ou o orçamento não foi criado pelo usuário.");
    }
    //João
    public function actionOrcamentoPorLaboratorio()
    {
        // Obter o token de autorização dos cabeçalhos da requisição
        $authorizationHeader = Yii::$app->getRequest()->getHeaders()->get('Authorization');
        $user = User::findByAccessToken($authorizationHeader);

        if (!$user) {
        throw new ForbiddenHttpException("Você não tem permissão para acessar este recurso.");
        }

        // Encontrar o utilizador correspondente ao usuário autenticado
        $utilizador = Utilizador::findOne(['user_id' => $user->id]);

        if (!$utilizador || !$utilizador->idLab) {
        throw new NotFoundHttpException("Utilizador não encontrado ou não associado a um laboratório.");
        }
        // Buscar os orçamentos do laboratório do utilizador, incluindo todos os estados e os serviços ativos
        $orcamentos = Orcamento::find()
        ->select('orcamento.*')
        ->where(['orcamento.laboratorio_id' => $utilizador->idLab])
        ->with([
            'estadoOrcamentos.estado' // Carrega todos os estados do orçamento
        ])
        ->asArray()
        ->all();

        // Buscar os serviços de cada orçamento individualmente
        foreach ($orcamentos as &$orcamento) {
            $orcamento['servicos'] = ServicoOrcamento::find()
                ->select('servico.*, servico_orcamento.quantidade')
                ->where(['servico_orcamento.orcamento_id' => $orcamento['id']])
                ->joinWith('servico', false) // Desabilita o eager loading do relacionamento 'servico'
                ->asArray()
                ->all();

            // Encontrar o estado mais recente (com base no ID) e adicionar ao resultado
            $ultimoEstado = null;
            foreach ($orcamento['estadoOrcamentos'] as &$estadoOrcamento) {
                if ($ultimoEstado === null || $estadoOrcamento['id'] > $ultimoEstado['id']) {
                    $ultimoEstado = $estadoOrcamento;
                }
                $estadoOrcamento['estado'] = $estadoOrcamento['estado']['estado'];
                unset($estadoOrcamento['estado_id']);
            }
            $orcamento['estado_orcamento'] = $ultimoEstado['estado'];
        }

        if (empty($orcamentos)) {
            throw new NotFoundHttpException("Não foram encontrados orçamentos para o laboratório do utilizador.");
        }

        return $orcamentos;
      
    }
    //gustavo
    public function actionOrcamentoPorLaboratorioComEstadoAceito()
    {
        // Obter o token de autorização dos cabeçalhos da requisição
        $authorizationHeader = Yii::$app->getRequest()->getHeaders()->get('Authorization');
        $user = User::findByAccessToken($authorizationHeader);

        if (!$user) {
        throw new ForbiddenHttpException("Você não tem permissão para acessar este recurso.");
        }

        // Encontrar o utilizador correspondente ao usuário autenticado
        $utilizador = Utilizador::findOne(['user_id' => $user->id]);

        if (!$utilizador || !$utilizador->idLab) {
        throw new NotFoundHttpException("Utilizador não encontrado ou não associado a um laboratório.");
        }

        // Buscar os orçamentos do laboratório do utilizador
        $orcamentos = Orcamento::find()
        ->select('orcamento.*')
        ->where(['laboratorio_id' => $utilizador->idLab])
        ->joinWith([
            'estadoOrcamentos' => function ($query) {
                $query->andWhere(['estado_orcamento.estado_id' => 1]); // Filtra pelo estado_id = 1 (aceito)
            },
            'estadoOrcamentos.estado' // Carrega os estados relacionados aos estados do orçamento
        ])
        ->with([
            'servicos' => function ($query) {
                $query->select(['servico.*', 'servico_orcamento.quantidade'])
                      ->innerJoin('servico_orcamento', 'servico.id = servico_orcamento.servico_id');
            },
        ])
        ->asArray()
        ->all();
       


        if (empty($orcamentos)) {
        throw new NotFoundHttpException("Não foram encontrados orçamentos para o laboratório do utilizador.");
        }
        return $orcamentos;
    }
    // endPoint para listar todos os orçamentos
    public function actionIndex()
    {
        $orcamentos = Orcamento::find()->all();
        return $orcamentos;
    }

    // endPoint para atualizar orcamentos
    public function actionUpdate($id)
    {
        $model = Orcamento::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException("O orçamento com ID $id não foi encontrado.");
        }

        // Carregar os dados do corpo da requisição para o modelo
        $model->load(Yii::$app->request->getBodyParams(), '');

        if ($model->save()) {
            return $model; 
        } else {
            return $model->getErrors();
        }
    }
}
