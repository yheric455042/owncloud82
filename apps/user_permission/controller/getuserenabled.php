<?php

namespace OCA\User_Permission\Controller;

use OCP\AppFramework\Http\Templateresponse;
use OCP\AppFramework\Controller;
use OCP\IRequest;
use OC\AllConfig;
use OCP\AppFramework\Http\DataResponse;

class GetUserEnabled extends Controller {
    
    public function __construct($AppName, IRequest $request) {
        parent::__construct($AppName, $request);
    }

    public function getEnabled(){

        $uids = \OCP\User::getUsers();
        $config = \OC::$server->getConfig();
        $userValue = $config->getUserValueForUsers('core', 'enabled', $uids);
        $max = sizeof($uids);

        for($i = 0; $i<$max; $i++){
            $name = $uids[$i];
            $userValue[$name] = (!array_key_exists($name, $userValue) || $userValue[$name] == 'true' || empty($userValue[$name])) ? true:false;
        }

        return new DataResponse($userValue);
    }
}
