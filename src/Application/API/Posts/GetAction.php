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

class GetAction extends ProtectedAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
         $id = $this->request->getAttribute('id');

        $dataService = new PostDataService();

        try {
            $data=$dataService->getById($this->request->getAttribute('id'));

            return $this->respondWithData(array('status' => 'ok','title'=>$data->title,'shortDesc'=>$data->shortDesc,'id'=>$data->getId()
                    ,'content'=>$data->content,'userId'=>$data->userId
                    ,'categoryId'=>$data->categoryId,'imageFile'=>$data->imageFile
                    ,'lastUpdated'=>$data->lastUpdated,'featuredPos'=>$data->featuredPos
                    
                    ), 200);
        } catch (Exception $e) {
            
        }
        return $this->respondWithError(new ActionError(ActionError::BAD_REQUEST), 400);
    }
}
