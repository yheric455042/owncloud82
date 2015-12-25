<<?php
style('user_permission', 'styles');
?>
ul>
	<li class='update'>
        <?php p($l->t('Your account is disabled now.')); ?><br/><br/>
        <?php p($l->t('Please contact your system administrator if this message appeared.')) ?><br/><br/>
        <a class="button" <?php print_unescaped(OC_User::getLogoutAttribute()); ?>><?php p($l->t("Redirect to login page")) ?></a>
	</li>
</ul>
