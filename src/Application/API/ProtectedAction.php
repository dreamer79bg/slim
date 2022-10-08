<?php

declare(strict_types=1);

namespace App\Application\API;

use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Application\API\Action;
use App\Application\API\ActionError;
use SlimSession\Helper as SessionHelper;

abstract class ProtectedAction extends Action {

    /**
     * @throws HttpNotFoundException
     * @throws HttpBadRequestException
     */
    public function __invoke(Request $request, Response $response, array $args): Response {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        $session = new SessionHelper();

        if ($session->exists('userId')) {
            $id = $session->get('userId');

            if ($id > 0) {
                return parent::__invoke($request, $response, $args);
            }
        }

        return $this->respondWithError(new ActionError(ActionError::NOT_ALLOWED, 'Access is denied'), 403);
    }

}
