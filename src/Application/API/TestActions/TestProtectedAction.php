<?php

declare(strict_types=1);

namespace App\Application\API\TestActions;

use App\Application\API\ProtectedAction;
use App\Application\API\ActionError;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


class TestProtectedAction extends ProtectedAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $data= array('test'=>1);
        return $this->respondWithData($data, 200);
    }
}
