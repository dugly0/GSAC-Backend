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
use app\models\Laboratorio;
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
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);

        return $actions;
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
        if (empty($post['descricao']) || empty($post['servico_orcamento'])) {
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
            $estadoOrcamento->estado_id = 3;
            $estadoOrcamento->data = date('Y-m-d'); // Formato apenas com dia, mês e ano
            $estadoOrcamento->save();

            for ($i = 0; $i < count($post['servico_orcamento']); $i++) {
                $servicoOrcamento = new ServicoOrcamento();
                $servicoOrcamento->orcamento_id = $model->id;
                $servicoOrcamento->servico_id = $post['servico_orcamento'][$i]["servico_id"];
                $servicoOrcamento->quantidade = $post['servico_orcamento'][$i]["quantidade"];
                $servicoOrcamento->save();
            }
            return $model;
        } else {
            throw new BadRequestHttpException("Falha ao criar o orçamento.");
        }
    }
    // endPoint para atualizar orcamentos
    public function actionUpdate($id)
    {
        // Obter o token da autorização dos cabeçalhos da solicitação
        $authorizationHeader = Yii::$app->getRequest()->getHeaders()->get('Authorization');
        // Encontrar o user correspondente ao usuário autenticado
        $user = User::findByAccessToken($authorizationHeader);
        // Buscar o utilizador correspondente ao token
        $utilizador = Utilizador::find()->where(['user_id' => $user->id])->one();       
        $model = Orcamento::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException("O orçamento com ID $id não foi encontrado.");
        }
        if($user->role_id == 1 || $utilizador -> idLab == $model->laboratorio_id || $utilizador->id == $model->utilizador_id ) {
            // Carregar os dados do corpo da requisição para o modelo
            $requestData = Yii::$app->getRequest()->getBodyParams();
            
            // Remover os campos que não devem ser atualizados            
            $requestData['id'] = $model -> id;
            $requestData['data_entrada'] = $model -> data_entrada;
            $requestData['utilizador_id'] = $model -> utilizador_id; 

            // Carregar os dados do corpo da requisição para o modelo        
            $model->load($requestData, '');
            if ($model->save()) {
                $estadoOrcamento = new EstadoOrcamento();
                $estadoOrcamento->orcamento_id = $model->id;
                $estadoOrcamento->estado_id = $requestData['estadoOrcamentos'];
                $estadoOrcamento->data = date('Y-m-d'); // Formato apenas com dia, mês e ano
                $estadoOrcamento->save();

                $servico = ServicoOrcamento::find()->where(['orcamento_id' => $model->id])->all();
                for ($i = 0; $i < count($servico); $i++) {
                    $servico[$i]->delete();
                }
                for ($i = 0; $i < count($requestData['servico_orcamento']); $i++) {
                    $servicoOrcamento = new ServicoOrcamento();
                    $servicoOrcamento->orcamento_id = $model->id;
                    $servicoOrcamento->servico_id = $requestData['servico_orcamento'][$i]["servico_id"];
                    $servicoOrcamento->quantidade = $requestData['servico_orcamento'][$i]["quantidade"];
                    $servicoOrcamento->save();
                }
                return $model;
            } else {
                return $model->getErrors();
            }
        }
        throw new ForbiddenHttpException("Não tem permissão para atualizar este orçamento.");
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
            ->joinWith([
                'estadoOrcamentos c' => function ($query) {
                    $query->innerJoin('estado b', 'c.estado_id = b.id')
                          ->select(['b.*', 'c.*'])
                          ->orderBy(['c.id' => SORT_DESC]); // Ordena pela data em ordem decrescente
                },
                'servicoOrcamentos e' => function ($query) {
                    $query->innerJoin('servico d', 'e.servico_id = d.id')
                          ->select(['d.*', 'e.*']);
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
            ->joinWith([
                'estadoOrcamentos c' => function ($query) {
                    $query->innerJoin('estado b', 'c.estado_id = b.id')
                          ->select(['b.*', 'c.*'])
                          ->orderBy(['c.id' => SORT_DESC]); // Ordena pela data em ordem decrescente
                },
                'servicoOrcamentos e' => function ($query) {
                    $query->innerJoin('servico d', 'e.servico_id = d.id')
                          ->select(['d.*', 'e.*']);
                }
            ])
            ->asArray()
            ->all();

        if (empty($orcamentos)) {
            throw new \yii\web\NotFoundHttpException("Não foram encontrados orçamentos para o utilizador com ID $utilizador->id.");
        }
        return $orcamentos;
    }     
    public function actionFindOrcamentoById($idOrcamento)
    {
        // Obter o token da autorização dos cabeçalhos da solicitação
        $authorizationHeader = Yii::$app->getRequest()->getHeaders()->get('Authorization');
        // Encontrar o user correspondente ao usuário autenticado
        $user = User::findByAccessToken($authorizationHeader);

        if ($user->role_id == 1) {
            // Retornar o orçamento especificado se o usuário for admin
            $orcamento = Orcamento::find()
            ->alias('a')
            ->where(['a.id' => $idOrcamento])
            ->joinWith([
                'estadoOrcamentos c' => function ($query) {
                    $query->innerJoin('estado b', 'c.estado_id = b.id')
                          ->select(['b.*', 'c.*'])
                          ->orderBy(['c.data' => SORT_DESC]); // Ordena pela data em ordem decrescente;
                },
                'servicoOrcamentos e' => function ($query) {
                    $query->innerJoin('servico d', 'e.servico_id = d.id')
                          ->select(['d.*', 'e.*']);
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
            ->where(['utilizador_id' => $utilizador->id, 'orcamento.id' => $idOrcamento])
            ->joinWith([
                'estadoOrcamentos c' => function ($query) {
                    $query->innerJoin('estado b', 'c.estado_id = b.id')
                          ->select(['b.*', 'c.*'])
                          ->orderBy(['c.id' => SORT_DESC]); // Ordena pela data em ordem decrescente
                },
                'servicoOrcamentos e' => function ($query) {
                    $query->innerJoin('servico d', 'e.servico_id = d.id')
                          ->select(['d.*', 'e.*']);
                }
            ])
            ->asArray()
            ->one();

        if (empty($orcamento)) {
            throw new \yii\web\NotFoundHttpException("Não foi encontrado orçamento com ID $idOrcamento para o utilizador com ID $utilizador->id ou o orçamento não foi criado pelo usuário.");
        }

        return $orcamento;
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
            
            'estadoOrcamentos.estado', // Carrega todos os estados do orçamento
            'utilizador' => function ($query) { // Carrega apenas o id e nome do utilizador
                $query->select(['id', 'nome'])->indexBy('id');
            },
            'laboratorio' => function ($query) { // Carrega apenas o id e nome do laboratório
                $query->select(['id', 'nome'])->indexBy('id');
            }
        ])
        ->asArray()
        ->all();

        // Encontrar o estado mais recente (com base no ID) e adicionar ao resultado
        foreach ($orcamentos as &$orcamento) {
            $ultimoEstado = null;
            foreach ($orcamento['estadoOrcamentos'] as &$estadoOrcamento) {
                if ($ultimoEstado === null || $estadoOrcamento['id'] > $ultimoEstado['id']) {
                    $ultimoEstado = $estadoOrcamento;
                }
                $estadoOrcamento['estado'] = $estadoOrcamento['estado']['estado'];
                unset($estadoOrcamento['estado_id']);
            }
            $orcamento['estado_orcamento'] = $ultimoEstado['estado'];

            // Buscar os serviços de cada orçamento individualmente
        foreach ($orcamentos as &$orcamento) {
            $orcamento['servicos'] = ServicoOrcamento::find()
                ->select('servico.*, servico_orcamento.quantidade')
                ->where(['servico_orcamento.orcamento_id' => $orcamento['id']])
                ->joinWith('servico', false) // Desabilita o eager loading do relacionamento 'servico'
                ->asArray()
                ->all();

            // ... (código para encontrar o estado mais recente)
        }
        }

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
    public function actionUpdateOrcamentoLab($id)
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

    public function actionUpdateServicoOrcamentoLab($orcamentoId, $servicoId)
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
    public function actionDeleteServicoOrcamento()
    {
       
        $authorizationHeader = Yii::$app->getRequest()->getHeaders()->get('Authorization');
        $token = str_replace('Bearer ', '', $authorizationHeader);

        if (!$token) {
            Yii::error("Token não encontrado no cabeçalho de autorização.");
            throw new ForbiddenHttpException("Token não encontrado.");
        }

        $user = User::findByAccessToken($token);

        if (!$user) {
            Yii::error("Usuário não encontrado para o token fornecido.");
            throw new ForbiddenHttpException("Token inválido.");
        }

        $utilizador = Utilizador::find()->where(['user_id' => $user->id])->one();

        $bodyParams = Yii::$app->request->bodyParams;
        $id = $bodyParams['id'];

        $model = ServicoOrcamento::findOne($id);

        if ($model === null) {
            Yii::error("Orçamento com ID $id não encontrado.");
            throw new NotFoundHttpException("O orçamento com ID $id não foi encontrado.");
        }

        if ($user->role_id == 1 || $utilizador->idLab == $model->laboratorio_id || $utilizador->id == $model->utilizador_id) {
            $servicoOrcamento = ServicoOrcamento::find()->where(['id' => $id])->one();

            if ($servicoOrcamento) {
                $servicoOrcamento->delete();
                Yii::$app->response->statusCode = 200;
                return ['message' => 'Serviço no orçamento removido com sucesso'];
            }

            Yii::error("Serviço com ID $id não encontrado no orçamento.");
            throw new NotFoundHttpException("Serviço não encontrado no orçamento.");
        } else {
            Yii::error("Usuário não autorizado a remover o orçamento com ID $id.");
            throw new ForbiddenHttpException("Você não tem permissão para remover este orçamento.");
        }
    }


    public function actionCreateEstadoOrcamentoLab()
    {
        $bodyParams = Yii::$app->request->bodyParams;
        $orcamentoId = $bodyParams['orcamentoId'];
        // Buscar o orçamento
        $orcamento = Orcamento::findOne($orcamentoId);
        if (!$orcamento) {
            throw new NotFoundHttpException("O orçamento com ID $orcamentoId não foi encontrado.");
        }

        // Lógica de autorização e verificação (reaproveitada)
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
        if ($orcamento->laboratorio_id !== $user->utilizador->idLab) {
            throw new ForbiddenHttpException("Você não tem permissão para criar um estado para este orçamento.");
        }

        // Criar um novo modelo EstadoOrcamento
        $estadoOrcamento = new EstadoOrcamento();
        $estadoOrcamento->orcamento_id = $orcamentoId; // Definir o ID do orçamento
        $estadoOrcamento->data = date('Y-m-d'); // Data atual

        // Carregar os dados da requisição (estado_id)
        $estadoOrcamento->load(Yii::$app->request->bodyParams["estado_id"], '');

        // Validação verificar se o estado_id é válido
        if (!Estado::findOne($estadoOrcamento->estado_id)) {
            throw new \yii\web\BadRequestHttpException("Estado não encontrado.");
        }

        if ($estadoOrcamento->save()) {
            Yii::$app->response->statusCode = 201; // Created
            return ['message' => 'Estado do orçamento criado com sucesso', 'estadoOrcamento' => $estadoOrcamento];
        } else {
            Yii::$app->response->statusCode = 422; // Unprocessable Entity
            return $estadoOrcamento->errors;
        }
    }
  
}
