<?php

declare(strict_types=1);

namespace App\Application\Actions\TestActions;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HelloWorldAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $this->response->getBody()->write('Hello world!');
        return $this->response;
    }
}
