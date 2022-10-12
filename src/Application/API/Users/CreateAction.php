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

class CreateAction extends ProtectedAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $payload = (string)$this->request->getBody();
        if ($payload != '') {
            $reqData = json_decode($payload,true);
            if (is_array($reqData)) {
                try {
                    $userService = new UserDataService();
                    $id= $userService->createUser($reqData);
                    $user= $userService->getById($id);
                } catch (Exception $e) {
                    return $this->respondWithError(new ActionError(ActionError::BAD_REQUEST), 400);
                }
                
                return $this->respondWithData(array('status'=>'ok','id'=>$user->id), 200);
            }
        }
        
        return $this->respondWithError(new ActionError(ActionError::BAD_REQUEST), 400);
    }
}
