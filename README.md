# slim
 
This is a test project for a blog.

The project uses:

    Slim v3
    Bootstrap v4
    TinyMCE
    JQuery 3.5
    PHPUnit


To have a clean install first open app/db.php and set dbname, dbuser and dbpass properly. 
    $dbconfig= array(
        'dbhost'=> '127.0.0.1',
        'dbuser'=> 'dbuser',
        'dbpass'=>'dbpass',
        'dbname' => 'slimblog',
        'dbport' => 3306,
    );

If you need to change the base path change app/mainconfig.php:
    'basePath' => '/slim', //change to empty or any other base path you want to use

Base path is set initially as development is done on XAMPP for windows in a separate directory for the project.

Install:
	composer install
	(calls migrations and tests too)

Update:
	composer update
	(calls migrations and tests too)

Run migrations:
	composer setup

Run tests:
	composer test


By default the setup migrations install a user super / 12345678 . Some predefined users and posts are also included.

Admin is accessibe through /admin. Example: http://127.0.0.1/slim/admin


APIs:

 $app->group($appBasePath . '/api', function (App $group) {
        $group->get('/test', 'APITestsController:data');
        $group->get('/testerror', 'APITestsController:error');
        $group->get('/testprotected', 'APITestsController:protected');
        $group->post('/login', 'SecurityController:login'); 
        $group->get('/logout', 'SecurityController:logout');

        $group->group('/users', function (App $group) {
            $group->get('/list', 'APIUsersController:list');
            $group->put('[/]', 'APIUsersController:create');
            $group->delete('/{id}', 'APIUsersController:delete');
            $group->get('/{id}', 'APIUsersController:get');
            // $group->post('[/]', \App\Application\API\Users\UpdateAction::class);
            $group->post('[/[{id}]]', 'APIUsersController:update');
        });

        $group->group('/posts', function (App $group) {
            $group->get('/list', 'APIPostsController:list');
            $group->put('[/]', 'APIPostsController:create');
            $group->delete('/{id}', 'APIPostsController:delete');
            $group->get('/{id}', 'APIPostsController:get'); 
            $group->post('[/[{id}]]', 'APIPostsController:update');
        });
    });

API is protected and for using it there must be a call to POST api/login with username and password(from database) before calling other methods.
POST http://127.0.0.1/slim/api/login
{
	"username":"super",
	"password":"12345678"
}

There is also api/logout call implemented.

Example POST method:
POST http://127.0.0.1/slim/api/users/225
{
        "userName":"super123544",
	"password":"12345678",
	"fullName":"123"
}

Example GET method:
GET http://127.0.0.1/slim/api/posts/list
GET http://127.0.0.1/slim/api/posts/1

For CRUD operations there is an own pseudo-ORM object implemented that controls CRUD operations based on the table configuration, validators and preprocessors of each child class.

There are own Database connection and query(Iterator for results) implemented.

Tests cover all core objects- CRUD, Database and Query, API actions, test html actions, login and logout through API, etc. 

Own App\Application\Controller class is implemented to allow access control and share common methods across controllers. 
Models(CRUD objects) are in the src/Application/CRUD folder. Additional data services are implemented and can be found in the src/Application/DataServices folder.

