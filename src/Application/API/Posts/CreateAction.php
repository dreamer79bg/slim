<?php

declare(strict_types=1);

namespace App\Application\API\Posts;

use App\Application\API\ProtectedAction;
use App\Application\API\ActionError;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use SlimSession\Helper as SessionHelper;
use App\Application\DataServices\PostDataService;
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
                    $dataService = new PostDataService();
                    $session = new SessionHelper();

                    if ($session->exists('userId')) {
                        $reqData['userId'] = $session->get('userId');
                    }
                    $id= $dataService->createPost($reqData);
                    $data= $dataService->getById($id);
                } catch (Exception $e) {
                    print $e->getMessage().$e->getFile().$e->getLine();
                    return $this->respondWithError(new ActionError(ActionError::BAD_REQUEST), 400);
                }
                
                return $this->respondWithData(array('status'=>'ok','id'=>$data->id), 200);
            }
        }
        
        return $this->respondWithError(new ActionError(ActionError::BAD_REQUEST), 400);
    }
}
