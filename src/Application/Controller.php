<?php

declare(strict_types=1);

namespace App\Application;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Views\Twig;
use SlimSession\Helper as SessionHelper;

/**
 * A custom controller class for MVC app
 * written by A. Markov
 */
abstract class Controller {

    protected App $app;
    protected ContainerInterface $container;
    protected Twig $view;
    protected SessionHelper $session;

    const ERROR_NOT_ALLOWED= 'Not allowed';
    const ERROR_BAD_DATA= 'Bad data';
    
    public function __construct(ContainerInterface $container) {
        $this->container= $container;
        $this->view= $this->container->get('view');
        $this->session= new SessionHelper();
    }

    protected function respondJSON(Response $response,array $payload=[], int $statusCode= 200): Response {
        $json = json_encode($payload, JSON_PRETTY_PRINT);
        
        $response->getBody()->write($json);

        return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus($statusCode);
    }

    protected function respondHTML(Response $response, string $viewName, array $data=[], int $statusCode= 200) {
        $this->view->render($response, $viewName, $data);
        return $this->response->withStatus($statusCode);
    }
    
    protected function fetchHTMLView(string $viewName, array $data=[]) {
        return $this->view->fetchBlock($viewName, $data+['baseAppPath'=>$this->container->get('settings')['basePath']]);
    }

    protected function respondJSONWithError(Response $response, string $error = Error, int $statusCode = 200): Response {
        return $this->respondJSON($response, array('error'=>$error,'statusCode'=>$statusCode), $statusCode);
    }

    protected function checkLogin(): bool {
        if ($this->session->exists('userId')) {
            $id = $this->session->get('userId');

            if ($id > 0) {
                return true;
            }
        } 
        
        return false;
    }
    
    protected function doLogout() {
        $this->session->clear();
    }
    
}
