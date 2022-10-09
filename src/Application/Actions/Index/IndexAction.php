<?php

declare(strict_types=1);

namespace App\Application\Actions\Index;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class IndexAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
         return $this->view('index.html');
    }
}
