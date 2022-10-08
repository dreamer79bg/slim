<?php

declare(strict_types=1);

namespace Tests\Application\Actions;

use Tests\TestCase;

class TwigTestActionTest extends TestCase
{
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();
      
        $request = $this->createRequest('GET', '/twigtest');
        $response = $app->handle($request);
        $payload = trim((string) $response->getBody());
        
        $this->assertEquals('<h1>Hello</h1>', $payload);
    }
}
