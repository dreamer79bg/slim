<?php

namespace App\Application\Services;

use SlimSession\Helper as SessionHelper;
use App\Application\DataServices\UserDataService;

class SecurityService {

    protected static SecurityService $service;
    protected SessionHelper $session;

    public function __construct() {
        $this->session = new SessionHelper();
    }

    public function doLogin($userName, $password) {
        $this->doLogout();

        $userService = new UserDataService();
        if (($id = $userService->getLoginId($userName, $password)) > 0) {
            $user = $userService->getById($id);
            $this->session->set('userId', $id);
            $this->session->set('userName', $user->userName);
            $this->session->set('fullName', $user->fullName);
        }
    }

    public function doLogout() {
        $this->session->clear();
    }

    public function checkLogin() {
        if ($this->session->exists('userId')) {
            $id = $this->session->get('userId');

            if ($id > 0) {
                return true;
            }
        }

        return false;
    }

    public function getUserId(){
        if ($this->session->exists('userId')) {
            $id = $this->session->get('userId');

            if (!empty($id)) {
                return $id;
            }
        }

        return false;
    }
    
    static function getService() {
        if (empty(self::$service)||!is_object(self::$service)) {
            $class = self::class;
            self::$service = new $class();
        }

        return self::$service;
    }

}
