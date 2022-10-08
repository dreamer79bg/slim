<?php

declare(strict_types=1);

namespace Tests\Application\API;

use Tests\TestCase;

class TestProtectedTest extends TestCase
{
    protected function testLogout($app){
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
        
        $data= json_decode($payload,true);
        $this->assertArrayHasKey('error', $data);
        $this->assertArrayNotHasKey('data', $data);
        $this->assertArrayHasKey('statusCode', $data);
        $this->assertEquals(403,$data['statusCode']);
    }
    
    protected function testLogin($app) {
        //perform login 
        //TBD - add user name and password
        $request = $this->createRequest('GET', '/api/login');
        
        try {
            $response = $app->handle($request);
        } catch (\Exception $e) {
        }
        
        $request = $this->createRequest('GET', '/api/testprotected');
        
        try {
            $response = $app->handle($request);
        } catch (\Exception $e) {
        }
        $payload = (string) $response->getBody();
        
        $data= json_decode($payload,true);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayNotHasKey('error', $data);
        $this->assertArrayHasKey('statusCode', $data);
        $this->assertEquals(200,$data['statusCode']);
    }
    
    public function testAction()
    {
        
        
        $app = $this->getAppInstance();

        $this->testLogout($app);
        $this->testLogin($app);
        //test that logout works 
        $this->testLogout($app);
    }
}
