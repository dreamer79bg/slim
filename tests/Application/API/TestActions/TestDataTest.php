<?php

declare(strict_types=1);

namespace Tests\Application\API\TestActions;

use Tests\TestCase;

class TestDataTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();
      
        $request = $this->createRequest('GET', '/api/test');
        
        try {
            $response = $app->handle($request);

        } catch (\Exception $e) {
            
        }
        $payload = (string) $response->getBody();
        
        $data= json_decode($payload,true);
        $this->assertContains(array('test'=>1), $data);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayNotHasKey('error', $data);
        $this->assertArrayHasKey('statusCode', $data);
        $this->assertEquals(200,$data['statusCode']);
    }
}
