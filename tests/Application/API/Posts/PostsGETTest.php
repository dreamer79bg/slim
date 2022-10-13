<?php

declare(strict_types=1);

namespace Tests\Application\API\Posts;

use Tests\Application\API\TestCase;

class PostsGETTest extends TestCase {

    /**
     * @covers App\Application\API\Posts\GetAction
     * @covers App\Application\API\Posts\DeleteAction
     * @covers App\Application\API\Posts\CreateAction
     * @covers App\Application\API\Security\LoginAction
     * @covers App\Application\API\Security\LogoutAction
     */
    public function testAction() {
        $this->doLogin();

        $app = $this->getAppInstance();

        $id = 0;
        $postData=  array(
            'userId' => 1, 'title' => 'Test title '.date('Y-m-d H:i:s'), 'shortDesc' => 'Невероятно, но факт- извънземните имат бази на Венера.', 'content'=>'Нещо си там да пишем.'
            , 'imageFile'=>'testimage'.mt_rand(1,5).'.jpg', 'featuredPos'=>0
            );
        $request = $this->createJsonRequest('PUT', '/api/posts',$postData)->withHeader('Content-Type', 'application/json'
                    
                    );
        $response = $app->handle($request);
        $status = $response->getStatusCode();
        $payload = (string) $response->getBody();
        $this->assertEquals(200, $status);
        if (true||$status == 200) {
            $payload = (string) $response->getBody();
            $data = json_decode($payload, true);
            $id = $data['id'];
        }

            $request = $this->createRequest('GET', '/api/posts/' . $id);
            $response = $app->handle($request);
            
            $this->assertEquals(200, $status);
          
            if ($status == 200) {
                $payload = (string) $response->getBody();
                
                $data = json_decode($payload, true);
                $this->assertEquals($id, $data['id']);
                $this->assertEquals($postData['title'], $data['title']);
                $this->assertEquals($postData['shortDesc'], $data['shortDesc']);
                $this->assertEquals($postData['content'], $data['content']);
                $this->assertEquals($postData['userId'], $data['userId']);
                $this->assertEquals($postData['featuredPos'], $data['featuredPos']);
                $this->assertEquals($postData['imageFile'], $data['imageFile']);
            }

        
        
        //delete the user if needed
        if (!empty($id)) {
            $request = $this->createRequest('DELETE', '/api/posts/' . $id);
            $response = $app->handle($request);
            $status = $response->getStatusCode();
            $this->assertEquals(200, $status);
            
            $request = $this->createRequest('GET', '/api/posts/' . $id);
            $response = $app->handle($request);
            $status = $response->getStatusCode();
            $this->assertEquals(400, $status);
            
        }

        $this->doLogout();
    }

}
