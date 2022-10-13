<?php

declare(strict_types=1);

namespace App\Application\Actions\Admin\Dialogs;

use App\Application\Actions\ProtectedAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Application\DataServices\UserDataService;

class CreatePostDialogAction extends ProtectedAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        return $this->view('admin/Dialogs/CreatePost.html');
    }
}
