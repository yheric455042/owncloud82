<?php
namespace OCA\Show_All_Activity\AppInfo;

use OC\Files\View;
use OCP\AppFramework\App;
use OCP\IContainer;
use OCA\Show_All_Activity\Controller\ShowController;

class Application extends App {

    public function __construct(array $urlParams=array()){
            parent::__construct('show_all_activity', $urlParams);

            $container = $this->getContainer();

            $container->registerService('ActivityApplication', function($c){
                return new \OCA\Activity\AppInfo\Application();
            });

            
            $container->registerService('ShowController', function($c){
                $server = $c->query('ServerContainer');

                return new ShowController(
                    $c->query('AppName'),
				    $server->getRequest(),
                    $c->query('ActivityApplication')->getContainer()->query('ActivityData'),
                    $c->query('ActivityApplication')->getContainer()->query('GroupHelper'),
                    $c->query('ActivityApplication')->getContainer()->query('Navigation'),
                    $c->query('ActivityApplication')->getContainer()->query('UserSettings'),
                    $server->getDateTimeFormatter(),
                    $server->getPreviewManager(),
                    $server->getURLGenerator(),
                    $server->getMimeTypeDetector(),
                    new View(''),
                    $c->query('ActivityApplication')->getContainer()->query('CurrentUID')
                );
            });
    }
}
