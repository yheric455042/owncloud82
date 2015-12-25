<?php
/**
 * ownCloud - files_mv
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author eotryx <mhfiedler@gmx.de>
 * @copyright eotryx 2015
 */

namespace OCA\Files_mv\AppInfo;
use OCA\Files_mv\Hooks\FilesHook;


\OCP\Util::addScript( 'files_mv', "move" );
\OCP\Util::addStyle('files_mv', 'mv');
/*
$app = new Application();
$container = $app->getContainer();

$container->query('FilesHook')->register();
*/
Fileshook::register();


