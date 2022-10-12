<?php

declare(strict_types=1);

namespace App\Application\API\Users;

use App\Application\API\ProtectedAction;
use App\Application\API\ActionError;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use SlimSession\Helper as SessionHelper;
use App\Application\DataServices\UserDataService;
use Exception;

class DeleteAction extends ProtectedAction {

    /**
     * {@inheritdoc}
     */
    protected function action(): Response {
        $id = $this->request->getAttribute('id');

        $userService = new UserDataService();

        try {
            $userService->deleteUser($this->request->getAttribute('id'));

            return $this->respondWithData(array('status' => 'ok'), 200);
        } catch (Exception $e) {
            
        }
        return $this->respondWithError(new ActionError(ActionError::BAD_REQUEST), 400);
    }

}
