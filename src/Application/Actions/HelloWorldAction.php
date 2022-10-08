<?php

declare(strict_types=1);

namespace App\Application\Actions;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HelloWorldAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function action(Request $request, Response $response): Response
    {
        $response->getBody()->write('Hello world!');
        return $response;
    }
}
