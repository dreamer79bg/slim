<?php

declare(strict_types=1);

namespace Tests\Application\API\TestActions;

use Tests\TestCase;

class TestProtectedTest extends TestCase {

    protected function testLogout($app) {
        //perform logout
        $request = $this->createRequest('GET', '/api/logout');

        try {
            $response = $app->handle($request);
        } catch (\Exception $e) {
            print $e->getMessage();
        }

        $request = $this->createRequest('GET', '/api/testprotected');
        try {
            $response = $app->handle($request);
        } catch (\Exception $e) {
            print $e->getMessage();
        }
        $payload = (string) $response->getBody();

        $data = json_decode($payload, true);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals(403, $response->getStatusCode());
    }

    protected function testLogin($app) {
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
            $this->assertArrayNotHasKey('error', $data);
            $this->assertEquals(200, $response->getStatusCode());
        } catch (\Exception $e) {
            
        }

        //rewrite all logins :D 

        $request = $this->createRequest('GET', '/api/testprotected');

        try {
            $response = $app->handle($request);
        } catch (\Exception $e) {
            
        }
        $payload = (string) $response->getBody();
        $data = json_decode($payload, true);
        $this->assertArrayNotHasKey('error', $data);
        $this->assertEquals(200, $response->getStatusCode());
    }

    protected function testBadLogin($app) {
        //perform login 
        //TBD - add user name and password
        $data = array('username' => 'super', 'password' => 'a1234568');
        $request = $this->createJsonRequest('POST',
                        '/api/login',
                        $data)->withHeader('Content-Type', 'application/json');

        $response = $app->handle($request);

        $payload = (string) $response->getBody();

        $data = json_decode($payload, true);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals(403, $response->getStatusCode());

        //rewrite all logins :D 

        $request = $this->createRequest('GET', '/api/testprotected');

        try {
            $response = $app->handle($request);
        } catch (\Exception $e) {
            
        }
        $payload = (string) $response->getBody();

        $data = json_decode($payload, true);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /**
     * @covers App\Application\API\TestActions\TestProtectedAction
     * @covers App\Application\API\Security\LoginAction
     * @covers App\Application\API\Security\LogoutAction
     */
    public function testAction() {


        $app = $this->getAppInstance();

        $this->testLogout($app);
        $this->testLogin($app);
        //test that logout works 
        $this->testLogout($app);
        $this->testBadLogin($app);
    }

}
