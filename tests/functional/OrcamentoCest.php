<?php

class OrcamentoCest
{
    // Propriedades da classe para armazenar os tokens
    public $token_user_comum = "9_hQg4sDkXdPpNS2PoaZwWVgIoppAzXZ";
    public $token_user_admin = "wK9DpRsBpCPjxyBTzkJ1fBU2-pbpaTyq";
    public $token_user_laboratorio = "-e4Co38nAcu9b08vtfHVhV64Aqllqzq9";
    public $id = 1;
    public $body_to_create = [
        "descricao"=> "dfsdffsd",
        "preco"=> 250,
        "data_entrega"=> "2024-05-20",      
        "laboratorio_id"=> 1,
        "servicoOrcamento"=> [
                [
                    "servico_id"=> 6,
                    "quantidade"=> 1
                ]
            ]            
        ];
    public $body_errado_to_create = [
        "preco"=> 250,
        "data_entrega"=> "2024-05-20",      
        "laboratorio_id"=> 1                 
        ];
    public $body_to_update = [
        "descricao"=> "dfsdffsd",
        "preco"=> 250,
        "data_entrega"=> "2024-05-20",  
        "laboratorio_id"=> 1,
        "estadoOrcamento"=>[
            [
                "estado_id"=> 5,
            ]
        ],
        "servicoOrcamento"=> [
                [
                    "servico_id"=> 6,
                    "quantidade"=> 1
                ]
            ]            
        ];
    public $body_to_update_errado = [
        "descricao"=> "dfsdffsd",
        "preco"=> 250,
        "data_entrega"=> "2024-05-20",  
        "laboratorio_id"=> 1                 
        ];
    public function testCreateOrcamento(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_comum);

        $body = $this->body_to_create;
        
        $I->sendPOST('/api/orcamento', $body);

        $I->seeResponseCodeIs(200);

        $I->seeResponseJsonMatchesJsonPath('$.descricao');
        $I->seeResponseJsonMatchesJsonPath('$.preco');
        $I->seeResponseJsonMatchesJsonPath('$.laboratorio_id');
        $I->seeResponseJsonMatchesJsonPath('$.servicoOrcamento[0].servico_id');
        $I->seeResponseJsonMatchesJsonPath('$.servicoOrcamento[0].quantidade');
        $I->seeResponseJsonMatchesJsonPath('$.estadoOrcamento[0].estado_id');

        
        
    }
    public function testCreateOrcamentoSemAutorizacao(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer teste');

        $body = $this->body_to_create;
        
        $I->sendPOST('/api/orcamento', $body);

        $I->seeResponseCodeIs(401);

        $I->seeResponseContainsJson([
            "name"=> "Unauthorized",
            "message"=> "Your request was made with invalid credentials.",
            "status"=> 401
        ]);
    }
    public function testCreateOrcamentoComTokenErrado(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_admin);

        $body = $this->body_to_create;
        
        $I->sendPOST('/api/orcamento', $body);

        $I->seeResponseCodeIs(403);

        $I->seeResponseContainsJson([
            "name"=> "Forbidden",
            "message"=> "Não tem permissão para criar orçamentos.",
            "status"=> 403
        ]);
    }
    public function testCreateOrcamentoBodyErrado(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_comum);

        $body = $this->body_errado_to_create;
        
        $I->sendPOST('/api/orcamento', $body);

        $I->seeResponseCodeIs(400);

        $I->seeResponseContainsJson([
            "name"=> "Bad Request",
            "message"=> "Faltam campos obrigatórios. descrição ou servicoOrcamento.",
            "status"=> 400
        ]);
    }
    public function testUpdateOrcamento(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_comum);

        $body = $this->body_to_update;
        $id = $this->id;
        $I->sendPUT('/api/orcamento/update?idOrcamento=' . $id, $body);

        $I->seeResponseCodeIs(200);

        $I->seeResponseJsonMatchesJsonPath('$.descricao');
        $I->seeResponseJsonMatchesJsonPath('$.preco');
        $I->seeResponseJsonMatchesJsonPath('$.laboratorio_id');
        $I->seeResponseJsonMatchesJsonPath('$.servicoOrcamento[0].servico_id');
        $I->seeResponseJsonMatchesJsonPath('$.servicoOrcamento[0].quantidade');
        $I->seeResponseJsonMatchesJsonPath('$.estadoOrcamento[0].estado_id');

    }
    public function testUpdateOrcamentoSemAutorizacao(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer teste');

        $body = $this->body_to_update;
        $id = $this->id;
        $I->sendPUT('/api/orcamento/update?idOrcamento=' . $id, $body);

        $I->seeResponseCodeIs(401);

        $I->seeResponseContainsJson([
            "name"=> "Unauthorized",
            "message"=> "Your request was made with invalid credentials.",
            "status"=> 401
        ]);
    }
    public function testUpdateOrcamentoComTokenErradoUsuario(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_comum);

        $body = $this->body_to_update;
        $id = 3;
        $I->sendPUT('/api/orcamento/update?idOrcamento=' . $id, $body);

        $I->seeResponseCodeIs(403);

        $I->seeResponseContainsJson([
            "name"=> "Forbidden",
            "message"=> "Não tem permissão para atualizar este orçamento.",
            "status"=> 403
        ]);
    }
    public function testUpdateOrcamentoComTokenErradoLaboratorio(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_laboratorio);

        $body = $this->body_to_update;
        $id = 3;
        $I->sendPUT('/api/orcamento/update?idOrcamento=' . $id, $body);

        $I->seeResponseCodeIs(403);

        $I->seeResponseContainsJson([
            "name"=> "Forbidden",
            "message"=> "Não tem permissão para atualizar este orçamento.",
            "status"=> 403
        ]);
    }
    public function testUpdateOrcamentoBodyErrado(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_comum);

        $body = $this->body_to_update_errado;
        $idOrcamento = $this->id;
        $I->sendPUT('/api/orcamento/'. $idOrcamento, $body);

        $I->seeResponseCodeIs(400);

        $I->seeResponseContainsJson([
            "name"=> "Bad Request",
            "message"=> "Missing required parameters: idOrcamento",
            "status"=> 400
        ]);
    }
    public function testGetOrcamentoGabinete(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_admin);

        $I->sendGET('/api/orcamento/');

        $I->seeResponseCodeIs(200);

        $I->seeResponseJsonMatchesJsonPath('$[0].id');
        $I->seeResponseJsonMatchesJsonPath('$[0].data_entrada');
        $I->seeResponseJsonMatchesJsonPath('$[0].descricao');
        $I->seeResponseJsonMatchesJsonPath('$[0].preco');
        $I->seeResponseJsonMatchesJsonPath('$[0].data_entrega');
        $I->seeResponseJsonMatchesJsonPath('$[0].fatura');
        $I->seeResponseJsonMatchesJsonPath('$[0].utilizador_id');
        $I->seeResponseJsonMatchesJsonPath('$[0].laboratorio_id');
        $I->seeResponseJsonMatchesJsonPath('$[0].estadoOrcamento[0].id');
        $I->seeResponseJsonMatchesJsonPath('$[0].estadoOrcamento[0].orcamento_id');
        $I->seeResponseJsonMatchesJsonPath('$[0].estadoOrcamento[0].estado_id');
        $I->seeResponseJsonMatchesJsonPath('$[0].estadoOrcamento[0].data');
        $I->seeResponseJsonMatchesJsonPath('$[0].servicoOrcamento[0].id');
        $I->seeResponseJsonMatchesJsonPath('$[0].servicoOrcamento[0].orcamento_id');
        $I->seeResponseJsonMatchesJsonPath('$[0].servicoOrcamento[0].servico_id');
        $I->seeResponseJsonMatchesJsonPath('$[0].servicoOrcamento[0].quantidade');

    }
    public function testGetOrcamentoUsuario(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_comum);

        $I->sendGET('/api/orcamento/');

        $I->seeResponseCodeIs(200);

        $I->seeResponseJsonMatchesJsonPath('$[0].id');
        $I->seeResponseJsonMatchesJsonPath('$[0].data_entrada');
        $I->seeResponseJsonMatchesJsonPath('$[0].descricao');
        $I->seeResponseJsonMatchesJsonPath('$[0].preco');
        $I->seeResponseJsonMatchesJsonPath('$[0].data_entrega');
        $I->seeResponseJsonMatchesJsonPath('$[0].fatura');
        $I->seeResponseJsonMatchesJsonPath('$[0].utilizador_id');
        $I->seeResponseJsonMatchesJsonPath('$[0].laboratorio_id');
        $I->seeResponseJsonMatchesJsonPath('$[0].estadoOrcamento[0].id');
        $I->seeResponseJsonMatchesJsonPath('$[0].estadoOrcamento[0].orcamento_id');
        $I->seeResponseJsonMatchesJsonPath('$[0].estadoOrcamento[0].estado_id');
        $I->seeResponseJsonMatchesJsonPath('$[0].estadoOrcamento[0].data');
        $I->seeResponseJsonMatchesJsonPath('$[0].servicoOrcamento[0].id');
        $I->seeResponseJsonMatchesJsonPath('$[0].servicoOrcamento[0].orcamento_id');
        $I->seeResponseJsonMatchesJsonPath('$[0].servicoOrcamento[0].servico_id');
        $I->seeResponseJsonMatchesJsonPath('$[0].servicoOrcamento[0].quantidade');

    }
    public function testGetOrcamentoLaboratorio(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_laboratorio);

        $I->sendGET('/api/orcamento/');

        $I->seeResponseCodeIs(200);

        $I->seeResponseJsonMatchesJsonPath('$[0].id');
        $I->seeResponseJsonMatchesJsonPath('$[0].data_entrada');
        $I->seeResponseJsonMatchesJsonPath('$[0].descricao');
        $I->seeResponseJsonMatchesJsonPath('$[0].preco');
        $I->seeResponseJsonMatchesJsonPath('$[0].data_entrega');
        $I->seeResponseJsonMatchesJsonPath('$[0].fatura');
        $I->seeResponseJsonMatchesJsonPath('$[0].utilizador_id');
        $I->seeResponseJsonMatchesJsonPath('$[0].laboratorio_id');
        $I->seeResponseJsonMatchesJsonPath('$[0].estadoOrcamento[0].id');
        $I->seeResponseJsonMatchesJsonPath('$[0].estadoOrcamento[0].orcamento_id');
        $I->seeResponseJsonMatchesJsonPath('$[0].estadoOrcamento[0].estado_id');
        $I->seeResponseJsonMatchesJsonPath('$[0].estadoOrcamento[0].data');
        $I->seeResponseJsonMatchesJsonPath('$[0].servicoOrcamento[0].id');
        $I->seeResponseJsonMatchesJsonPath('$[0].servicoOrcamento[0].orcamento_id');
        $I->seeResponseJsonMatchesJsonPath('$[0].servicoOrcamento[0].servico_id');
        $I->seeResponseJsonMatchesJsonPath('$[0].servicoOrcamento[0].quantidade');

    }
    public function testGetOrcamentoSemToken(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer teste');

        $I->sendGET('/api/orcamento/');

        $I->seeResponseCodeIs(401);

        $I->seeResponseContainsJson([
            "name"=> "Unauthorized",
            "message"=> "Your request was made with invalid credentials.",
            "status"=> 401
        ]);

    }
    
}
