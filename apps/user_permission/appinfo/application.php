<?php
namespace OCA\User_Permission\AppInfo;

use \OCP\AppFramework\App;

use \OCA\User_Permission\Hooks\UserHooks;
use \OCA\User_Permission\Hooks\LoginHooks;
use \OCA\User_Permission\Controller\GetUserEnabled;
use \OCA\User_Permission\Controller\ChangeUserEnabled;


class Application extends App {

    public function __construct(array $urlParams=array()){
        parent::__construct('user_permission', $urlParams);

        $container = $this->getContainer();

        /**
         * Controllers
         */
        $container->registerService('LoginHooks', function($c) {
            return new LoginHooks(
                $c->query('ServerContainer')->getUserSession()
            );
        });

        $container->registerService('UserHooks', function($c) {
            return new UserHooks(
                $c->query('ServerContainer')->getUserManager()
            );
        });

        $container->registerService('L10N', function($c) {
            return $c->query('ServerContainer')->getL10N($c->query('AppName'));
        });
        
        $container->registerService('ChangeUserEnabledController', function($c){
            return new ChangeUserEnabled(
                $c->query('AppName'),
                $c->query('Request')
            );
        });

        $container->registerService('GetUserEnabledController', function($c){
            return new GetUserEnabled(
                $c->query('AppName'),
                $c->query('Request')
            );
        });

    }
}

