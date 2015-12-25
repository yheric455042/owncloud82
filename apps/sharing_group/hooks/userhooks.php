<?php
namespace OCA\Sharing_Group\Hooks;

use OC_User;
use OCA\Sharing_Group\Data;
class UserHooks { 

    private $userManager;

    public function __construct($userManager) {
        $this->userManager = $userManager;
    }

    public function DeleteUser($user) {
        Data::removeUserFromGroup($user->getUID());
        Data::removeUserFromOwner($user->getUID());
    }

    public function register() {
        $callback = array($this, 'DeleteUser');
        $this->userManager->listen('\OC\User', 'postDelete', $callback);
    }
}

?>
