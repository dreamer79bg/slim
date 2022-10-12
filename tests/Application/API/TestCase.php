<?php

declare(strict_types=1);

namespace Tests\Application\API;

use Exception;
use Tests\TestCase as MainTestCase;

class TestCase extends MainTestCase {

    /**
     * @covers App\Application\API\Security\LoginAction
     */
    protected function doLogin() {
        $app= $this->getAppInstance();
        
        //perform login 
        //TBD - add user name and password
        $data = array('username' => 'super', 'password' => '12345678');
        $request = $this->createJsonRequest('POST',
                        '/api/login',
                        $data)->withHeader('Content-Type', 'application/json');

        try {
            $response = $app->handle($request);
            $payload = (string) $response->getBody();
            $data = json_decode($payload, true);
            $this->assertArrayHasKey('data', $data);
            $this->assertArrayNotHasKey('error', $data);
            $this->assertArrayHasKey('statusCode', $data);
            $this->assertEquals(200, $data['statusCode']);
        } catch (\Exception $e) {
            
        }
    }

    /**
     * @covers App\Application\API\Security\LogoutAction
     */
    protected function doLogout() {
        $app= $this->getAppInstance();
        
        //perform logout
        $request = $this->createRequest('GET', '/api/logout');

        try {
            $response = $app->handle($request);
        } catch (\Exception $e) {
            print $e->getMessage();
        }
    }
}
