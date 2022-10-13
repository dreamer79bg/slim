<?php

declare(strict_types=1);

namespace App\Application\Actions\Index;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Application\DataServices\PostDataService;

class IndexAction extends Action {

    /**
     * {@inheritdoc}
     */
    protected function action(): Response {
        $dataService = new PostDataService();
        $data = $dataService->getAll();

        try {
            $html = '';
            foreach ($data as $row) {
                $html .= $this->renderModule('datablocks/post.html', ['data' => $row]);
            }
        } catch (\Exception $e) {
            
        }

        return $this->view('index.html', ['viewHTML' => $html]);
    }

}
