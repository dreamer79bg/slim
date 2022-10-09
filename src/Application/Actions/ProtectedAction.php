<?php

declare(strict_types=1);

namespace App\Application\Actions;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use SlimSession\Helper as SessionHelper;

abstract class ProtectedAction extends Action {

    /**
     * @throws HttpNotFoundException
     * @throws HttpBadRequestException
     */
    public function __invoke(Request $request, Response $response, array $args): Response {
        $this->request = $request;

        $session = new SessionHelper();

        $parsedBody = $request->getParsedBody();

        if ($request->getMethod() == 'POST') {
            if (!empty($parsedBody['doLogin']) && $parsedBody['doLogin'] == 'login') {
                $session->clear(); //clear all old session info
                $session->set('userId', 1);
            }
        }

        $parsedQuery= $request->getQueryParams();
        if (isset($parsedQuery['logout'])) {
            $session->clear(); //clear all old session info
        }

        $this->response = $response;
        $this->args = $args;

        if ($session->exists('userId')) {
            $id = $session->get('userId');

            if ($id > 0) {
                return parent::__invoke($request, $response, $args);
            }
        }

        return $this->actionLogin();
    }

    protected function actionLogin(): Response {
        return $this->view('admin/login.html', [], 403);
    }

}
