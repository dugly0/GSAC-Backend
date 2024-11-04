<?php

class GabineteCest
{
    // Propriedades da classe para armazenar os tokens
    public $token_user_admin = "LnkeNnnbkd3N5WZiYOR_9RE8k33nK1RX";

    public function testGetUsers(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_admin);

        $I->sendGET('/api/user/');

        // Verificar se a resposta é um array
        $I->seeResponseIsJson();

        // Verificar se o status da resposta é 200
        $I->seeResponseCodeIs(200);

        // Verificar se contém pelo menos um usuário
        $I->seeResponseJsonMatchesJsonPath('$[*]'); // Verifica que existem múltiplos usuários

        // Decodificar a resposta JSON
        $users = json_decode($I->grabResponse(), true);

        // Verifica se a resposta é um array e contém pelo menos um usuário
        $I->assertIsArray($users);
        $I->assertNotEmpty($users);

        // Verificar os campos esperados para cada usuário
        foreach ($users as $user) {
            $I->assertArrayHasKey('id', $user);
            $I->assertArrayHasKey('role_id', $user);
            $I->assertArrayHasKey('status', $user);
            $I->assertArrayHasKey('email', $user);
            $I->assertArrayHasKey('username', $user);
            $I->assertArrayHasKey('password', $user);
            $I->assertArrayHasKey('auth_key', $user);
            $I->assertArrayHasKey('access_token', $user);
            $I->assertArrayHasKey('logged_in_ip', $user);
            $I->assertArrayHasKey('logged_in_at', $user);
            $I->assertArrayHasKey('created_ip', $user);
            $I->assertArrayHasKey('created_at', $user);
            $I->assertArrayHasKey('updated_at', $user);
            $I->assertArrayHasKey('banned_at', $user);
            $I->assertArrayHasKey('banned_reason', $user);
            $I->assertArrayHasKey('utilizador', $user);
        }
    }   
    public function testGetUsersUnauthorized(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer teste');
        
        $I->sendPOST('/api/user');

        $I->seeResponseCodeIs(401);

        $I->seeResponseContainsJson([
            "name"=> "Unauthorized",
            "message"=> "Your request was made with invalid credentials.",
            "status"=> 401
        ]);
    }

    public function testDeleteUserSuccess(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_admin);
        $utilizadorId = 3;

        // Requisição HTTP DELETE
        $I->sendDELETE("/api/user/{$utilizadorId}");
        // Código 204 confirmando o user excluido
        $I->seeResponseCodeIs(204);

        // Requisição HTTP GET
        $I->sendGET("/api/user/{$utilizadorId}");
        // Código 404 indicando que o utilizador não foi encontrado
        $I->seeResponseCodeIs(404);
    }

    public function testDeleteUserUnauthorized(FunctionalTester $I)
    {
        $utilizadorId = 3;
        $I->haveHttpHeader('Authorization', 'Bearer teste');
        $I->sendPOST("/api/user/{$utilizadorId}");
        $I->seeResponseCodeIs(401);
        $I->seeResponseContainsJson([
            "name" => "Unauthorized",
            "message" => "Your request was made with invalid credentials.",
            "status" => 401
        ]);
    }


    public function testDeleteUserNotFound(FunctionalTester $I)
    {
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_admin);
        $utilizadorId = 999;
        $I->sendDELETE("/api/user/{$utilizadorId}");
        $I->seeResponseCodeIs(404);
        $I->seeResponseContainsJson([
            "name" => "Not Found",
            "message" => "Utilizador não encontrado.",
            "status" => 404
        ]);
    }

    public function testRegisterUserSuccess(FunctionalTester $I)
    {
        // Dados válidos para o novo usuário
        $userData = [
            'email' => 'test@example.com',
            'username' => 'testuser',
            'password' => 'Password123',
            'role_id' => 2, // Supondo que o ID 2 é válido para o teste
        ];

        // Configurando o cabeçalho de autorização
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_admin);

        // Enviando a requisição POST para o endpoint de registro
        $I->sendPOST('/api/user/register', $userData);

        // Verificando se o status de resposta é 200 (OK)
        $I->seeResponseCodeIs(200);

        // Verificando se a resposta contém os dados do usuário registrado
        $I->seeResponseContainsJson([
            'email' => $userData['email'],
            'username' => $userData['username'],
        ]);

        // Validando o retorno JSON e que contém o ID do usuário registrado
        $I->seeResponseJsonMatchesJsonPath('$.id');
        $I->seeResponseJsonMatchesJsonPath('$.role_id');
    }

    public function testRegisterUserInvalidRole(FunctionalTester $I)
    {
        // Dados com um role_id inválido
        $userData = [
            'email' => 'test_invalid@example.com',
            'username' => 'testuser_invalid',
            'password' => 'Password123',
            'role_id' => 9999, // ID de role inválido para o teste
        ];

        // Configurando o cabeçalho de autorização
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_admin);

        // Enviando a requisição POST para o endpoint de registro
        $I->sendPOST('/api/user/register', $userData);

        // Verificando se o status de resposta é 400 (Bad Request)
        $I->seeResponseCodeIs(400);

        // Verificando se a resposta contém a mensagem de erro
        $I->seeResponseContainsJson([
            'name' => 'Bad Request',
            'message' => 'Role inválido.',
        ]);
    }

    public function testRegisterUserMissingData(FunctionalTester $I)
    {
        
        // Dados incompletos para registro
        $userData = [
            'username' => 'missingemailuser',
            // 'password' => 'Password123',
            "email" => "a58070@alunos.ipb.pt",
        ];

        // Configurando o cabeçalho de autorização
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_admin);

        // Enviando a requisição POST para o endpoint de registro
        $I->sendPOST('/api/user/register', $userData);

        // Verificando se o status de resposta é 422 (Data Validation Failed)
        $I->seeResponseCodeIs(422);

        // Verificando se a resposta contém a mensagem de erro apropriada
        $I->seeResponseContainsJson([
            "field" => "password",
            "message"=> "Password cannot be blank."
            
        ]);
    }

    public function testRegisterUserWithInvalidToken(FunctionalTester $I)
{
    // Dados válidos para o registro do usuário
    $userData = [
        'email' => 'invalidtoken@example.com',
        'username' => 'invalidtokenuser',
        'password' => 'Password123',
    ];

    // Configurando um cabeçalho de autorização com um token incorreto
    $I->haveHttpHeader('Authorization', 'Bearer token_invalido');

    // Enviando a requisição POST para o endpoint de registro
    $I->sendPOST('/api/user/register', $userData);

    // Verificando se o status de resposta é 401 (Unauthorized)
    $I->seeResponseCodeIs(401);

    // Verificando se a resposta contém a mensagem de erro de autorização
    $I->seeResponseContainsJson([
        "name" => "Unauthorized",
        "message" => "Your request was made with invalid credentials.",
        "status" => 401,
    ]);
}

public function testUpdateUserSuccess(FunctionalTester $I)
{
    // Dados válidos para atualização do usuário
    $userId = 2; // ID do usuário que será atualizado
    $updateData = [
        
        'username' => 'updateduser',
        'password' => 'NewPassword123',
    ];

    // Configurando o cabeçalho de autorização
    $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_admin);

    // Enviando a requisição PUT para o endpoint de atualização
    $I->sendPUT("/api/user/{$userId}", $updateData);

    // Verificando se o status de resposta é 200 (OK)
    $I->seeResponseCodeIs(200);

    // Verificando se a resposta contém os dados atualizados do usuário
    $I->seeResponseContainsJson([
        'message' => "Informações atualizadas com sucesso"
    ]);
}

public function testUpdateUserInvalidData(FunctionalTester $I)
    {
        
        $userId = 10; // ID do usuário que será atualizado
        $updateData = [
            'username' => 'invaliduser', 
            'password' => 'NewPassword123',
            'nif' => 'teste'
        ];

        // Configurando o cabeçalho de autorização
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_admin);

        // Enviando a requisição PUT para o endpoint de atualização
        $I->sendPUT("/api/user/{$userId}", $updateData);

        // Verificando se o status de resposta é 422 (Unprocessable Entity)
        $I->seeResponseCodeIs(422);

        // Verificando se a resposta contém a mensagem de erro apropriada
        $I->seeResponseContainsJson([
            "nif" => [
            "Nif must be an integer."
        ]
        ]);
    }

    public function testUpdateUserNonExistentId(FunctionalTester $I)
    {
        // Dados válidos para atualização do usuário inexistente
        $userId = 9000; // ID de um usuário que não existe
        $updateData = [
            'email' => 'nonexistent@example.com',
            'username' => 'nonexistentuser',
            'password' => 'NewPassword123',
        ];
    
        // Configurando o cabeçalho de autorização
        $I->haveHttpHeader('Authorization', 'Bearer ' . $this->token_user_admin);
    
        // Enviando a requisição PUT para o endpoint de atualização com um ID inexistente
        $I->sendPUT("/api/user/{$userId}", $updateData);
    
        // Verificando se o status de resposta é 404 (Not Found)
        $I->seeResponseCodeIs(404);
    
        // Verificando se a resposta contém uma mensagem de erro apropriada
        $I->seeResponseContainsJson([
            "name" => "Not Found",
            "message" => "Utilizador não encontrado.",
        ]);
    }
public function testUpdateUserWithInvalidToken(FunctionalTester $I)
{
    // Dados válidos para atualização do usuário
    $userId = 2; // ID do usuário que será atualizado
    $updateData = [
        'email' => 'invalidtoken@example.com',
        'username' => 'invalidtokenuser',
        'password' => 'Password123',
    ];

    // Configurando um cabeçalho de autorização com um token incorreto
    $I->haveHttpHeader('Authorization', 'Bearer token_invalido');

    // Enviando a requisição PUT para o endpoint de atualização
    $I->sendPUT("/api/user/{$userId}", $updateData);

    // Verificando se o status de resposta é 401 (Unauthorized)
    $I->seeResponseCodeIs(401);

    // Verificando se a resposta contém a mensagem de erro de autorização
    $I->seeResponseContainsJson([
        "name" => "Unauthorized",
        "message" => "Your request was made with invalid credentials.",
        "status" => 401,
    ]);
}
}
