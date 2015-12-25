<?php
/**
 * ownCloud - ajax_event_dispatcher
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author simon <simon.l@inwinstack.com>
 * @copyright simon 2015
 */

namespace OCA\Ajax_Event_Dispatcher\AppInfo;

use OCP\AppFramework\App;

$app = new App('ajax_event_dispatcher');
$container = $app->getContainer();

\OCP\Util::addScript( 'ajax_event_dispatcher', "EventDispatcher");
\OCP\Util::addScript( 'ajax_event_dispatcher', "listener");
