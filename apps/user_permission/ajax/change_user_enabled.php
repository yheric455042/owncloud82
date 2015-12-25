<?php

if($_POST['checked'] === 'false')
  OC_User::disableUser($_POST['user']);
else
  OC_User::enableUser($_POST['user']);
