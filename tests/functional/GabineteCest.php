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
    
}
