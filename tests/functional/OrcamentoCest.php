<?php

class OrcamentoCest
{
    // Propriedades da classe para armazenar os tokens
    public $token_user_comum = "O4YNJqGq103jkeBH7lmt82L5ByRVw1bn";
    public $token_user_admin = "LnkeNnnbkd3N5WZiYOR_9RE8k33nK1RX";
    public $id = 1;
    public $body_to_create = [
        "descricao"=> "dfsdffsd",
        "preco"=> 250,
        "data_entrega"=> "2024-05-20",      
        "laboratorio_id"=> 1,
        "servico_orcamento"=> [
                [
                    "servico_id"=> 6,
                    "quantidade"=> 1
                ]
            ]            
        ];
    public $body_errado = [
        "preco"=> 250,
        "data_entrega"=> "2024-05-20",      
        "laboratorio_id"=> 1,
        "servico_orcamento"=> [
                [
                    "servico_id"=> 6,
                    "quantidade"=> 1
                ]
            ]            
        ];
    public $body_to_update = [
        "descricao"=> "dfsdffsd",
        "preco"=> 250,
        "data_entrega"=> "2024-05-20",  
        "fatura"=> null,
        "utilizador_id"=> 3,   
        "laboratorio_id"=> 1,
        "estadoOrcamentos"=>[
            [
                "estado_id"=> 5,
                "data"=> "2024-04-13"
            ]
        ],
        "servicoOrcamentos"=> [
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
        "fatura"=> null,
        "utilizador_id"=> 3,   
        "laboratorio_id"=> 1,        
        "servicoOrcamentos"=> [
                [
                    "servico_id"=> 6,
                    "quantidade"=> 1
                ]
            ]            
        ];
    public function testCreateOrcamento(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_comum);

        $body = $this->body_to_create;
        
        $I->sendPOST('/api/orcamento', $body);

        $I->seeResponseCodeIs(200);

        $I->seeResponseIsJson();
    }
    public function testCreateOrcamentoSemAutorizacao(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer teste');

        $body = $this->body_to_create;
        
        $I->sendPOST('/api/orcamento', $body);

        $I->seeResponseCodeIs(401);

        $I->seeResponseIsJson();
    }
    public function testCreateOrcamentoBodyErrado(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_comum);

        $body = $this->body_errado;
        
        $I->sendPOST('/api/orcamento', $body);

        $I->seeResponseCodeIs(400);

        $I->seeResponseIsJson();
    }
    public function testUpdateOrcamento(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_admin);

        $body = $this->body_to_update;
        $id = $this->id;
        $I->sendPUT('/api/orcamento/'. $id, $body);

        $I->seeResponseCodeIs(200);

        $I->seeResponseIsJson();
    }
    public function testUpdateOrcamentoSemAutorizacao(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer teste');

        $body = $this->body_to_update;
        $id = $this->id;
        $I->sendPUT('/api/orcamento/'. $id, $body);

        $I->seeResponseCodeIs(401);

        $I->seeResponseIsJson();
    }
    public function testUpdateOrcamentoBodyErrado(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_admin);

        $body = $this->body_to_update_errado;
        $id = $this->id;
        $I->sendPUT('/api/orcamento/'. $id, $body);

        $I->seeResponseCodeIs(500);

        $I->seeResponseIsJson();
    }
    public function testUpdateOrcamentoIdErrado(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_admin);

        $body = $this->body_to_update;
        $id = $this->id;
        $I->sendPUT('/api/orcamento/30', $body);

        $I->seeResponseCodeIs(404);

        $I->seeResponseIsJson();
    }
}
