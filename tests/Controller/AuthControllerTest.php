<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;

class AuthControllerTest extends AbstractControllerTest
{

    public function testSignUp(): void
    {
        $this->client->request('POST', '/api/v1/signUp', content: json_encode([
            'firstName' => 'Test',
            'lastName' => 'Test',
            'email' => 'test@test.com',
            'password' => '11111111',
            'confirmPassword' => '11111111'
        ]));

        $responseContent = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();

        $this->assertJsonDocumentMatchesSchema($responseContent, [
            'type' => 'object',
            'required' => ['token', 'refresh_token'],
            'properties' => [
                'token' => ['type' => 'string'],
                'refresh_token' => ['type' => 'string']
            ]
        ]);
    }
}
