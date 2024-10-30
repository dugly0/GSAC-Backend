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
    
    public function testLoginFailedPass(FunctionalTester $I)
    {
        $body = [
            'email' => 'neo',
            'password' => 'neo123'
        ];
        $I->sendPOST('/api/auth/login', $body);
        $I->seeResponseCodeIs(422);
        $I->seeResponseContainsJson([
            "field"=> "password",
            "message"=> "Incorrect password"
        ]);
        
    }
    public function testLoginFailedUser(FunctionalTester $I)
    {
        $body = [
            'email' => 'neo123',
            'password' => 'neo'
        ];
        $I->sendPOST('/api/auth/login', $body);
        $I->seeResponseCodeIs(422);
        $I->seeResponseContainsJson([
            "field"=> "email",
            "message"=> "Email / Username not found"
        ]);
    }
    public function testRegister(FunctionalTester $I)
{
    // Corpo da requisição JSON para registro de um usuário
    $body = [
        
            "username" => "newuser",
            "newPassword"=> "123456",
            "nome"=> "David",
            "nif"=>"123456789",
            "cod_postal"=> "1234567",
            "endereco"=> "Endereço do Usuário",
            "telefone"=> "999 999 999",
            "email"=> "newuser@example.com"
        
    ];

    // Envia uma requisição POST para a rota de registro
    $I->sendPOST('/api/auth/register', $body);

    // Verifica se o código de resposta é 200 (OK)
    $I->seeResponseCodeIs(200);

    // Verifica que a resposta é do tipo JSON
    $I->seeResponseIsJson();
}
public function testRegisterUser(FunctionalTester $I)
{
    // Corpo da requisição JSON para registro de um usuário
    $body = [
        
            "username" => "neo",
            "newPassword"=> "123456",
            "nome"=> "David",
            "nif"=>"123456789",
            "cod_postal"=> "1234567",
            "endereco"=> "Endereço do Usuário",
            "telefone"=> "999 999 999",
            "email"=> "newuser@example.com"
        
    ];

    // Envia uma requisição POST para a rota de registro
    $I->sendPOST('/api/auth/register', $body);

    // Verifica se o código de resposta é 422
    $I->seeResponseCodeIs(422);

    // Verifica que a resposta é do tipo JSON
}
public function testRegisterEmail(FunctionalTester $I)
{
    // Corpo da requisição JSON para registro de um usuário
    $body = [
        
            "username" => "newuser",
            "newPassword"=> "123456",
            "nome"=> "David",
            "nif"=>"123456789",
            "cod_postal"=> "1234567",
            "endereco"=> "Endereço do Usuário",
            "telefone"=> "999 999 999",
            "email"=> "neo@neo.com"
        
    ];

    // Envia uma requisição POST para a rota de registro
    $I->sendPOST('/api/auth/register', $body);

    // Verifica se o código de resposta é 422
    $I->seeResponseCodeIs(422);

    // Verifica que a resposta é do tipo JSON
}
public function testRegisterFailedNif(FunctionalTester $I)
{
    // Corpo da requisição JSON para registro de um usuário
    $body = [
        
            "username" => "newuser",
            "newPassword"=> "123456",
            "nome"=> "David",
            "nif"=>"123456789a",
            "cod_postal"=> "1234567",
            "endereco"=> "Endereço do Usuário",
            "telefone"=> "999 999 999",
            "email"=> "newuser@example.com"
        
    ];

    // Envia uma requisição POST para a rota de registro
    $I->sendPOST('/api/auth/register', $body);

    // Verifica se o código de resposta é 500 
    $I->seeResponseCodeIs(500);

    // Verifica que a resposta é do tipo JSON
    $I->seeResponseIsJson();
}

}
