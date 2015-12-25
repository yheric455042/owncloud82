<?php

namespace OCA\User_Permission\Controller;

use OCP\AppFramework\Http\Templateresponse;
use OCP\AppFramework\Controller;
use OCP\IRequest;

class ChangeUserEnabled extends Controller {
    
    public function __construct($AppName, IRequest $request) {
        parent::__construct($AppName, $request);
    }

    public function changeEnabled($checked, $user){
        ($checked === 'false') ? \OC_User::disableUser($user) : \OC_User::enableUser($user);
    }
}
