<?php


use OC\AllConfig;
$uids = OC_User::getUsers();

$config = \OC::$server->getConfig();
$userValue = $config->getUserValueForUsers('core', 'enabled', $uids);
$max = sizeof($uids);


for($i = 0; $i<$max; $i++){
    $name = $uids[$i];
    $userValue[$name] = ($userValue[$name] == 'true' || empty($userValue[$name])) ? true:false;
}

$userValue = json_encode($userValue);
echo $userValue;
