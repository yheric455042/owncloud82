<?php
/**
 * ownCloud - show_all_activity
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author eric <eric.y@inwinstack.com>
 * @copyright eric 2015
 */

namespace OCA\Show_All_Activity\AppInfo;

if (\OC_User::isAdminUser(\OC_User::getUser())) {
    \OCP\Util::addScript( 'show_all_activity', "script" );
    \OCP\Util::addStyle( 'activity', "style" );
    \OCP\Util::addStyle( 'show_all_activity', "style" );
}
