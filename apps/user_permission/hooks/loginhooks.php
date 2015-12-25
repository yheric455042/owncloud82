<?php
namespace OCA\User_Permission\Hooks;

use OC_User;
use OC_Template;

class LoginHooks {

    private $userSession;
   

    public function __construct($userSession){
        $this->userSession = $userSession;
    }


    public function CheckUser($user){
        if(!OC_User::isEnabled($user) && OC_User::userExists($user)){
            header('HTTP/1.1 401 Service Temporarily Unavailable');
            header('Status: 401 Service Temporarily Unavailable');

            $template = new \OC_Template('user_permission', 'userdisable', 'guest');
            $template->printPage();
            die();

        }
    }
 
    
    public function register(){
        $callback =array($this, 'CheckUser');
        $this->userSession->listen('\OC\User', 'preLogin', $callback);
    }

}
