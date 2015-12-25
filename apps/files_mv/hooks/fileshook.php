<?php

namespace OCA\Files_mv\Hooks;

use OCP\Util;

class FilesHook {
    
    private $userFolder;

    public function __construct($userFolder) {
        $this->userFolder = $userFolder;
    }
    
    public static function getSourceAndTarget($params) {
        file_put_contents('path.txt',print_r($params,true),FILE_APPEND);
    }

    public static function register() {
        //$callback = array($this,'getSourceAndTarget');
        //$this->userFolder->listen('\OC\Files','postRename',$callback);

		Util::connectHook('OC_Filesystem', 'post_rename', 'OCA\Files_mv\Hooks\FilesHook', 'getSourceAndTarget');
    }
}

?>
