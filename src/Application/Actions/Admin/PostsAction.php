<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin;

use App\Application\Actions\ProtectedAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Application\DataServices\PostDataService;

class PostsAction extends ProtectedAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $dataService= new PostDataService();
        $data= $dataService->getAll();
        
        $view= $this->renderModule('admin/posts.html', ['posts'=>$data]);
        return $this->view('admin/index.html',['viewHTML'=>$view]);
    }
}
