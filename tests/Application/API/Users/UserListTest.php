<?php

declare(strict_types=1);

namespace Tests\Application\API\Users;

use Tests\Application\API\TestCase;

class UserListTest extends TestCase
{
    /**
     * @covers App\Application\API\Users\ListAction
     * @covers App\Application\API\Security\LoginAction
     * @covers App\Application\API\Security\LogoutAction
     */
    public function testAction()
    {
        $this->doLogin();
        
        $app = $this->getAppInstance();

        $userName = 'test' . time().mt_rand(0,99999);
        $password = microtime(false);
        $fullName = 'test testov';
        
        $request = $this->createRequest('GET', '/api/users/list');
        $response = $app->handle($request);
        $status = $response->getStatusCode();
        $this->assertEquals(200, $status);
        $payload= (string) $response->getBody();
        $data= json_decode($payload,true);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('userName', $data[0]);
        $this->assertArrayHasKey('fullName', $data[0]);
        
        //try security- no login
        $userName = 'test' . time(). mt_rand(0,99999999);
        $password = microtime(false);
        $fullName = 'test testov';

        $this->doLogout();
        $request = $this->createRequest('GET', '/api/users/list');
        $response = $app->handle($request);
        $status = $response->getStatusCode();
        $this->assertEquals(403, $status);

        
    }
}
