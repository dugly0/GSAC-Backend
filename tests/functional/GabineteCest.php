<?php

class GabineteCest
{
    // Propriedades da classe para armazenar os tokens
    public $token_user_admin = "wK9DpRsBpCPjxyBTzkJ1fBU2-pbpaTyq";

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
        Yii::$app->mailer->useFileTransport = true;
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
}
