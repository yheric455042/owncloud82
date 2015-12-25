<?php
/**
 * ownCloud - user_permission
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Dino Peng <dino.p@inwinstack.com>
 * @copyright Dino Peng 2015
 */

/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\MyApp\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */

namespace OCA\User_Permission\AppInfo;

$application = new Application();

$application->registerRoutes($this, ['routes' => [
    ['name' => 'ChangeUserEnabled#changeEnabled', 'url' => '/changeEnabled', 'verb' => 'POST'],
    ['name' => 'GetUserEnabled#getEnabled', 'url' => '/getEnabled', 'verb' => 'GET'],
    
]]);
