<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\ProtectedAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class IndexAction extends ProtectedAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
         return $this->view('hello.html');
    }
}
