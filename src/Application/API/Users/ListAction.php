<?php

declare(strict_types=1);

namespace App\Application\API\Users;

use App\Application\API\ProtectedAction;
use App\Application\API\ActionError;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use SlimSession\Helper as SessionHelper;
use App\Application\DataServices\UserDataService;

class ListAction extends ProtectedAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $userService= new UserDataService();
        $data= $userService->getAll();
        return $this->respondWithData($data, 200);
    }
}
