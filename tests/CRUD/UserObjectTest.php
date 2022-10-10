<?php

declare(strict_types=1);

namespace Tests\Application\Actions\Admin;

use Tests\TestCase;
use App\Application\CRUD\UserObject;
use Exception;

class UserObjectTest extends TestCase
{
    public function testAction()
    {
        $userName= 'test'.time();
        $realPass= microtime(false);
        $password= sha1($realPass);
        $fullName= 'test testov';
        
        $user= new UserObject();
        $user->userName= $userName;
        $user->password= $password;
        $user->fullName= $fullName;
        $user->save();
        $id= $user->getId();
        
        $user= new UserObject($id);
        $this->assertEquals($userName, $user->userName);
        $this->assertEquals($id, $user->getId());
        $this->assertEquals($fullName, $user->fullName);
        $this->assertEquals($password, $user->password);
        
        //check read only props
        $ok= 0;
        try {
            $user->userName= 'aaa';
        } catch (Exception $e) {
            $ok=1;
        }
        $this->assertEquals(1, $ok);
        
        $ok= 0;
        try {
            $user->createdAt= date('Y-m-d H:i:s');
        } catch (Exception $e) {
            $ok=1;
        }
        $this->assertEquals(1, $ok);

        //check non existent props
        
        $ok= 0;
        try {
            $user->createdA9t= date('Y-m-d H:i:s');
        } catch (Exception $e) {
            $ok=1;
        }
        $this->assertEquals(1, $ok);
        
        //check delete operation
        $user->delete();
        
        $user= new UserObject(); //just clear user var for the next test as inside exception old value is kept
        
        $ok=0;
        try {
            $user= new UserObject($id);
        } catch (Exception $e) {
            $ok=1;
        }
        
        $this->assertEquals(null, $user->getId());
        $this->assertEquals(null, $user->userName);
        $this->assertEquals(null, $user->fullName);
        $this->assertEquals(null, $user->password);
        $this->assertEquals(1, $ok);
        
    }
}


