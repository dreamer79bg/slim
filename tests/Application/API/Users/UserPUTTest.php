<?php

declare(strict_types=1);

namespace Tests\Application\API\Users;

use Tests\Application\API\TestCase;

class UserPUTTest extends TestCase
{
    public function testAction()
    {
        $this->doLogin();
        
        $app = $this->getAppInstance();

        $userName = 'test' . time().mt_rand(0,99999);
        $password = microtime(false);
        $fullName = 'test testov';
        
        $request = $this->createJsonRequest('PUT', '/api/users',array('userName'=>$userName,'fullName'=>$fullName,'password'=>$password))->withHeader('Content-Type', 'application/json');
        $response = $app->handle($request);
        $status = $response->getStatusCode();
        $this->assertEquals(200, $status);
        
        //retry same
        $request = $this->createJsonRequest('PUT', '/api/users',array('userName'=>$userName,'fullName'=>$fullName,'password'=>$password))->withHeader('Content-Type', 'application/json');
        $response = $app->handle($request);
        $status = $response->getStatusCode();
        
        $this->assertEquals(400, $status);
        
        $userName = 'test' . time().mt_rand(0,99999);
        $password = microtime(false);
        $fullName = 'test testov';
        
        //try with id 
        $request = $this->createJsonRequest('PUT', '/api/users',array('id'=>123,'userName'=>$userName,'fullName'=>$fullName,'password'=>$password))->withHeader('Content-Type', 'application/json');
        $response = $app->handle($request);
        $status = $response->getStatusCode();
        $this->assertEquals(400, $status);
        
        
        //try security- no login
        $userName = 'test' . time(). mt_rand(0,99999999);
        $password = microtime(false);
        $fullName = 'test testov';

        $this->doLogout();
        $request = $this->createJsonRequest('PUT', '/api/users',array('userName'=>$userName,'fullName'=>$fullName,'password'=>$password))->withHeader('Content-Type', 'application/json');
        $response = $app->handle($request);
        $status = $response->getStatusCode();
        $this->assertEquals(403, $status);

        
    }
}
