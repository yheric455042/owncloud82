<?php

namespace OCA\User_Permission;

use OCP\IPreFilter;
use OC_User;

class UserPermissionPreFilter implements IPreFilter{

    public function dopre() {
        $user = OC_User::getUser();

        if(!$user) return false;

        if(!OC_User::isEnabled($user) && OC_User::userExists($user)){
            header('HTTP/1.1 401 Unauthorized');
            header('Status: 401 Unauthorized');

            $template = new \OC_Template('user_permission', 'userdisable', 'guest');
            $template->printPage();

            die();
        }

    }
}
