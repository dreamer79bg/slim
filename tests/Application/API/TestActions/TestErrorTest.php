<?php

declare(strict_types=1);

namespace Tests\Application\API\TestActions;

use Tests\TestCase;

class TestErrorTest extends TestCase
{
    /**
     * @covers App\Application\API\TestActions\TestErrorAction
     */
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();
      
        $request = $this->createRequest('GET', '/api/testerror');
        
        try {
            $response = $this->handleRequest($request);

        } catch (\Exception $e) {
            
        }
        $payload = (string) $response->getBody();
        
        $data= json_decode($payload,true);
        $this->assertArrayHasKey('error', $data);
        $this->assertArrayHasKey('statusCode', $data);
        $this->assertEquals(404,$data['statusCode']);
    }
}
