# slim
 
This is a test project for a blog.

To have a clean install first open app/db.php and set dbname, dbuser and dbpass properly. 

Install:
	composer install
	(calls migrations and tests too)

Update:
	composer update
	(calls migrations and tests too)

Run migrations:
	composer setup
	
APIs:

 $app->group('/api', function (Group $group) {
        $group->get('/test', \App\Application\API\TestActions\TestDataAction::class);
        $group->get('/testerror', \App\Application\API\TestActions\TestErrorAction::class);
        $group->get('/testprotected', \App\Application\API\TestActions\TestProtectedAction::class);
        $group->post('/login', \App\Application\API\Security\LoginAction::class);
        $group->get('/logout', \App\Application\API\Security\LogoutAction::class);
        
        $group->group('/users',   function (Group $group) {
            $group->get('/list', \App\Application\API\Users\ListAction::class);
            $group->put('[/]', \App\Application\API\Users\CreateAction::class);
            $group->delete('/{id}', \App\Application\API\Users\DeleteAction::class);
            $group->get('/{id}', \App\Application\API\Users\GetAction::class);
           // $group->post('[/]', \App\Application\API\Users\UpdateAction::class);
            $group->post('[/[{id}]]', \App\Application\API\Users\UpdateAction::class);
        });
        
        $group->group('/posts',   function (Group $group) {
            $group->get('/list', \App\Application\API\Posts\ListAction::class);
            $group->put('[/]', \App\Application\API\Posts\CreateAction::class);
            $group->delete('/{id}', \App\Application\API\Posts\DeleteAction::class);
            $group->get('/{id}', \App\Application\API\Posts\GetAction::class);
            $group->post('[/[{id}]]', \App\Application\API\Posts\UpdateAction::class);
        });
    });

By default the application works on apache dir with a base /slim. If needed to run on other base just change $app->setBasePath('/slim'); in app/routes.php.

By default the setup migrations install a use super / 12345678 .

Admin is accessibe through /admin.