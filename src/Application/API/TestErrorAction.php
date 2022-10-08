<?php

declare(strict_types=1);

namespace App\Application\API;

use App\Application\API\Action;
use App\Application\API\ActionError;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


class TestErrorAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        return $this->respondWithError(new ActionError(ActionError::BAD_REQUEST), 404);
    }
}
