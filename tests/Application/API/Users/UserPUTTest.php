<?php

declare(strict_types=1);

namespace Tests\Application\API\Users;

use Tests\Application\API\TestCase;

class UserPUTTest extends TestCase
{
    /**
     * @covers App\Application\API\Users\CreateAction
     * @covers App\Application\API\Users\DeleteAction
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
        
        $id=0;
        $request = $this->createJsonRequest('PUT', '/api/users',array('userName'=>$userName,'fullName'=>$fullName,'password'=>$password))->withHeader('Content-Type', 'application/json');
        $response = $app->handle($request);
        $status = $response->getStatusCode();
        $this->assertEquals(200, $status);
        if ($status==200) {
            $payload= (string) $response->getBody();
            $data= json_decode($payload,true);
            $id= $data['id'];
        }
        
        //retry same
        $request = $this->createJsonRequest('PUT', '/api/users',array('userName'=>$userName,'fullName'=>$fullName,'password'=>$password))->withHeader('Content-Type', 'application/json');
        $response = $app->handle($request);
        $status = $response->getStatusCode();
        
        $this->assertEquals(400, $status);
        
        //delete the user if needed
        if (!empty($id)) {
            $request = $this->createRequest('DELETE', '/api/users/'.$id);
            $response = $app->handle($request);
            $status = $response->getStatusCode();
            $this->assertEquals(200, $status);
        }
        
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
