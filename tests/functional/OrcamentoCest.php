<?php

class OrcamentoCest
{
    // Propriedades da classe para armazenar os tokens
    public $token_user_comum = "O4YNJqGq103jkeBH7lmt82L5ByRVw1bn";
    public $token_user_admin = "LnkeNnnbkd3N5WZiYOR_9RE8k33nK1RX";
    public $body = [
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

    public function _before(FunctionalTester $I)
    {
        // Executado antes de cada teste
    }

    public function _after(FunctionalTester $I)
    {
        // Executado após cada teste
    }

    // Teste da rota de login
    public function testCreateOrcamento(FunctionalTester $I)
    {
        // Define o header de autorização usando a propriedade da classe
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_comum);

        $body = $this->body;
        
        // Envia uma requisição POST para a rota de orçamentos
        $I->sendPOST('/api/orcamento', $body); // Não esqueça de incluir o corpo da requisição

        // Verifica se o código de resposta é 200 (OK)
        $I->seeResponseCodeIs(200);

        // Verifica que a resposta é do tipo JSON
        $I->seeResponseIsJson();
    }

    public function testCreateOrcamentoSemAutorizacao(FunctionalTester $I)
    {
        // Define o header de autorização usando a propriedade da classe
        $I->haveHttpHeader('Authorization', 'Bearer teste');

        $body = $this->body;
        
        // Envia uma requisição POST para a rota de orçamentos
        $I->sendPOST('/api/orcamento', $body); // Não esqueça de incluir o corpo da requisição

        // Verifica se o código de resposta é 200 (OK)
        $I->seeResponseCodeIs(401);

        // Verifica que a resposta é do tipo JSON
        $I->seeResponseIsJson();
    }
    public function testCreateOrcamentoBodyErrado(FunctionalTester $I)
    {
        // Define o header de autorização usando a propriedade da classe
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_comum);

        $body = $this->body_errado;
        
        // Envia uma requisição POST para a rota de orçamentos
        $I->sendPOST('/api/orcamento', $body); // Não esqueça de incluir o corpo da requisição

        // Verifica se o código de resposta é 200 (OK)
        $I->seeResponseCodeIs(400);

        // Verifica que a resposta é do tipo JSON
        $I->seeResponseIsJson();
    }
}
