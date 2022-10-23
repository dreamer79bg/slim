<?php

declare(strict_types=1);

namespace App\Application\API\Security;

use App\Application\API\Action;
use App\Application\API\ActionError;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use SlimSession\Helper as SessionHelper;
use App\Application\DataServices\UserDataService;

class LoginAction extends Action {

    /**
     * {@inheritdoc}
     */
    protected function action(): Response {
        $payload = (string)$this->request->getBody();
        
        if ($payload != '') {
            $reqData = json_decode($payload, true);
            
            if (is_array($reqData) && isset($reqData['username']) && isset($reqData['password'])) {
                $userService = new UserDataService();

                $session = new SessionHelper();
                $session->clear();

                if ($id = $userService->getLoginId($reqData['username'], $reqData['password'])) {
                    $user = $userService->getById($id);
                    $session->set('userId', $id);
                    $session->set('userName', $user->userName);
                    $session->set('fullName', $user->fullName);

                    return $this->respondWithData(array('fullName' => $user->fullName), 200);
                }
            }
        }
        
        return $this->respondWithError(new ActionError(ActionError::NOT_ALLOWED), 403);
    }

}
