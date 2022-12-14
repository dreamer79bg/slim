<?php

namespace App\Application\Controllers;

use App\Application\Controller as AppController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\RequestInterface as Request;
use App\Application\DataServices\UserDataService;

class SecurityController extends AppController {

    public function login(Request $request, Response $response, array $args = []): Response {
        $this->doLogout();

        $payload = (string) $request->getBody();
        
        if ($payload != '') {
            $reqData = json_decode($payload, true);

            if (is_array($reqData) && isset($reqData['username']) && isset($reqData['password'])) {
                
                $this->securityService->doLogin($reqData['username'], $reqData['password']);
                
                if ($id = $this->securityService->getUserId()) {
                    $userService= new UserDataService();
                    $user = $userService->getById($id);
                    return $this->respondJSON($response,array('fullName' => $user->fullName), 200);
                }
            }
        }

        return $this->respondJSONWithError($response,self::ERROR_NOT_ALLOWED, 403);
    }

    public function logout(Request $request, Response $response, array $args = []): Response {
        $this->doLogout();
        return $this->respondJSON($response,array('ok' => 'ok'), 200);
    }
    
}
