<?php
namespace OCA\User_Permission\Hooks;

use OC_User;
use OC_Template;

class UserHooks {

    private $userManager;
   

    public function __construct($userManager){
        $this->userManager = $userManager;
    }


    public function EnableUser($user, $password){
        $uid = $user->getUID();
        $config = \OC::$server->getConfig();
        $config->setUserValue($uid, 'core', 'enabled', 'true');
    }
 
    
    public function register(){
        $callback = array($this, 'EnableUser');
        $this->userManager->listen('\OC\User', 'postCreateUser', $callback);
    }

}
