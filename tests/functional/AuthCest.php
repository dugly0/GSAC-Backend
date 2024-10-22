<?php

class AuthCest
{
    public function _before(FunctionalTester $I)
    {
        // Executado antes de cada teste
    }

    public function _after(FunctionalTester $I)
    {
        // Executado após cada teste
    }

    // Teste da rota de login
    public function testLogin(FunctionalTester $I)
    {
        // Corpo da requisição JSON
        $body = [
            'email' => 'neo',
            'password' => 'neo'
        ];

        // Envia uma requisição POST para a rota de login
        $I->sendPOST('/api/auth/login', $body);

        // Verifica se o código de resposta é 200 (OK)
        $I->seeResponseCodeIs(200);

        // Verifica se a resposta contém o token de acesso e outros campos esperados
        $I->seeResponseContainsJson([
            'access_token' => 'LnkeNnnbkd3N5WZiYOR_9RE8k33nK1RX',
            'role_id' => 1,
            'user_id' => 1
        ]);

        // Verifica que a resposta é do tipo JSON
        $I->seeResponseIsJson();
    }
    public function testLoginFailed(FunctionalTester $I)
    {
        $body = [
            'email' => 'neo',
            'password' => 'neo123'
        ];
        $I->sendPOST('/api/auth/login', $body);
        $I->seeResponseCodeIs(422);
    }
}
