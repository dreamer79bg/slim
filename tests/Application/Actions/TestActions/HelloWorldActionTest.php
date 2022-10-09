<?php

declare(strict_types=1);

namespace Tests\Application\Actions\TestActions;

use Tests\TestCase;

class HelloWorldActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();
      
        $request = $this->createRequest('GET', '/hellotest');
        
        try {
            $response = $app->handle($request);

        } catch (\Exception $e) {
            
        }
        $payload = (string) $response->getBody();
        
        $this->assertEquals('Hello world!', $payload);
    }
}
