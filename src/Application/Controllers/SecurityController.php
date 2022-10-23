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
                
                $userService = new UserDataService();

                if ($id = $userService->getLoginId($reqData['username'], $reqData['password'])) {
                    $user = $userService->getById($id);
                    $this->session->set('userId', $id);
                    $this->session->set('userName', $user->userName);
                    $this->session->set('fullName', $user->fullName);

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
