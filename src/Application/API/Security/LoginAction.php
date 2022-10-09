<?php

declare(strict_types=1);

namespace App\Application\API\Security;

use App\Application\API\Action;
use App\Application\API\ActionError;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use SlimSession\Helper as SessionHelper;

class LoginAction extends Action
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $session = new SessionHelper();
        $session->set('userId',1);
        return $this->respondWithData(array(), 200);
    }
}
