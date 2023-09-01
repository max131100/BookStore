<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;

class AdminControllerTest extends AbstractControllerTest
{

    public function testGrantAuthor(): void
    {
        $user = $this->createUser('user@user.com', '11111111');

        $username = 'admin@admin.com';
        $password = '22222222';
        $admin = $this->createAdmin($username, $password);

        $this->auth($username, $password);

        $this->client->request('POST', '/api/v1/admin/grantAuthor/'.$user->getId());

        $this->assertResponseIsSuccessful();
    }
}
