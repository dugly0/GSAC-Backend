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
            $orcamentos = Orcamento::find()
            //retornar todos os orçamentos se user for admin
            //->where(['utilizador_id' => $user->id])
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
            throw new \yii\web\NotFoundHttpException("Não foram encontrados orçamentos para o utilizador com ID $user->id.");
        }
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

    
    public function actionUpdateOrcamento($id)
    {
        $model = Orcamento::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException("O orçamento com ID $id não foi encontrado.");
        }
        // Lógica de autorização e verificação
        $authorizationHeader = Yii::$app->request->headers->get('Authorization');
        $user = User::findByAccessToken($authorizationHeader); 

        if (!$user) {
            throw new ForbiddenHttpException("Você não tem permissão para acessar este recurso.");
        }

        // Forçamos o carregamento do Utilizador (mesmo que já tenha sido carregado)
        $user->populateRelation('utilizador', $user->getUtilizador()->one()); 

        // Agora o utilizador está garantidamente carregado
        if (!$user->utilizador->idLab) { 
            throw new NotFoundHttpException("Utilizador não associado a um laboratório.");
        }

        // Verificamos se o laboratório do orçamento corresponde ao laboratório do utilizador
        if ($model->laboratorio_id !== $user->utilizador->idLab) {
            throw new ForbiddenHttpException("Você não tem permissão para atualizar este orçamento.");
        }

        // Carregar os dados do corpo da requisição para o modelo (após a verificação)
        $model->load(Yii::$app->request->bodyParams, '');

        if ($model->save()) {
            Yii::$app->response->statusCode = 200; // Código de sucesso
            return ['message' => 'Data de entrega atualizada com sucesso', 'orcamento' => $model]; // Retorna o orçamento atualizado
        } else {
            Yii::$app->response->statusCode = 422; // Unprocessable Entity
            return $model->errors;
        }
    }

    public function actionUpdateServicoOrcamento($orcamentoId, $servicoId)
    {
        // Buscar o registro na tabela servico_orcamento
        $servicoOrcamento = ServicoOrcamento::findOne([
            'orcamento_id' => $orcamentoId,
            'servico_id' => $servicoId,
        ]);
    
        if (!$servicoOrcamento) {
            throw new NotFoundHttpException("Registro não encontrado na tabela servico_orcamento.");
        }
    
        // Lógica de autorização e verificação (adaptada do seu código)
        $authorizationHeader = Yii::$app->request->headers->get('Authorization');
        $user = User::findByAccessToken($authorizationHeader);
    
        if (!$user) {
            throw new ForbiddenHttpException("Você não tem permissão para acessar este recurso.");
        }
    
        // Forçamos o carregamento do Utilizador (mesmo que já tenha sido carregado)
        $user->populateRelation('utilizador', $user->getUtilizador()->one());
    
        // Agora o utilizador está garantidamente carregado
        if (!$user->utilizador->idLab) {
            throw new NotFoundHttpException("Utilizador não associado a um laboratório.");
        }
    
        // Buscar o orçamento para verificar o laboratório
        $orcamento = Orcamento::findOne($orcamentoId);
        if (!$orcamento) {
            throw new NotFoundHttpException("Orçamento não encontrado.");
        }
    
        // Verificamos se o laboratório do orçamento corresponde ao laboratório do utilizador
        if ($orcamento->laboratorio_id !== $user->utilizador->idLab) {
            throw new ForbiddenHttpException("Você não tem permissão para atualizar este serviço no orçamento.");
        }
    
        // Carregar os dados da requisição (servico_id e quantidade)
        $servicoOrcamento->load(Yii::$app->request->bodyParams, '');
    
        // Validação adicional (opcional): verificar se o novo servico_id pertence ao laboratório
        $servico = Servico::findOne($servicoOrcamento->servico_id);
        if (!$servico || $servico->laboratorio_id !== $orcamento->laboratorio_id) {
            throw new ForbiddenHttpException("O serviço não pertence ao laboratório do orçamento.");
        }
    
        if ($servicoOrcamento->save()) {
            Yii::$app->response->statusCode = 200;
            return ['message' => 'Serviço no orçamento atualizado com sucesso', 'servicoOrcamento' => $servicoOrcamento];
        } else {
            Yii::$app->response->statusCode = 422;
            return $servicoOrcamento->errors;
        }
    }
  
}
