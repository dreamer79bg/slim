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

class UpdateAction extends ProtectedAction
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
                    if (empty($reqData['id'])) {
                        $reqData['id']= $this->request->getAttribute('id', null);
                    }
                    
                    $dataService = new PostDataService();
                    $dataService->updatePost($reqData);
                    $data= $dataService->getById($reqData['id']);
                } catch (Exception $e) {
                    return $this->respondWithError(new ActionError(ActionError::BAD_REQUEST), 400);
                }
                
                return $this->respondWithData(array('status'=>'ok','id'=>$data->id), 200);
            }
        }
        
        return $this->respondWithError(new ActionError(ActionError::BAD_REQUEST), 400);
    }
}
