<?php

declare(strict_types=1);

namespace Tests\Application\Actions\Admin;

use Tests\TestCase;

class LoginTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();
      
        //logout first
        $request = $this->createRequest('GET', '/admin')->withQueryParams(array('logout'=>1));
        $response = $app->handle($request);
        $status = $response->getStatusCode();
        $this->assertEquals(403, $status);
        
        //simulate post of login page and check if admin index(status 200) is returned
        $request = $this->createRequest('POST', '/admin')->withParsedBody(array('doLogin'=>'login','username'=>'super','password'=>'12345678'));
        $response = $app->handle($request);
        $status = $response->getStatusCode();
        $this->assertEquals(200, $status);
        
        //logout
        $request = $this->createRequest('GET', '/admin')->withQueryParams(array('logout'=>1));
        $response = $app->handle($request);
        $status = $response->getStatusCode();
        $this->assertEquals(403, $status);
        
        //simulate post of login page and check if admin index(status 200) is returned
        $request = $this->createRequest('POST', '/admin')->withParsedBody(array('doLogin'=>'login','username'=>'super','password'=>'a1234568'));
        $response = $app->handle($request);
        $status = $response->getStatusCode();
        $this->assertEquals(403, $status);
        
    }
}
