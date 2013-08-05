<?php
    /** @var User $object */

    $prefix = "user";

    if ( empty( $errors ) ) $errors = array();
	if ( empty( $jsonErrors ) ) $jsonErrors = '{}';

    if ( !empty($errors["fatal"] ) ) {
		?><h3 class="error"><?= LocaleLoader::Translate( 'errors.fatal.' . $errors["fatal"] ); ?></h3><?
	}
?>
<div class="tabs">
	<?= FormHelper::FormHidden( 'selectedTab', !empty( $selectedTab ) ? $selectedTab : 0, 'selectedTab' ); ?>
    <ul class="tabs-list">
        <li><a href="#page-0">{lang:vt.common.commonInfo}</a></li>
    </ul>

    <div id="page-0" class="tab-page rows">
        <div data-row="login" class="row required">
            <label>{lang:vt.user.login}</label>
            <?= FormHelper::FormInput( $prefix . '[login]', $object->login, 'login', null, array( 'size' => 40 ) ); ?>
        </div>
        <div data-row="password" class="row required">
            <label>{lang:vt.user.password}</label>
            <?= FormHelper::FormPassword( 'password', $password, 'password', null, array( 'size' => 40 ) ); ?>
        </div>
        <div data-row="statusId" class="row required">
            <label>{lang:vt.user.statusId}</label>
            <?= FormHelper::FormSelect( $prefix . '[statusId]', StatusUtility::$Common[$__currentLang], "", "", $object->statusId, null, null, false ); ?>
        </div>
	</div>
</div>
<script type="text/javascript">
	var jsonErrors = {$jsonErrors};
</script>
 