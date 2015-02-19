<?php
    /** @var User $object */
    /** @var string $password */


    use Eaze\Helpers\FormHelper;
    use Eaze\Helpers\JsHelper;

    $prefix = 'user';

    if ( empty( $errors ) ) {
        $errors = [];
    }

    JsHelper::PushLine( sprintf( 'var jsonErrors = %s;', !empty( $jsonErrors ) ? $jsonErrors : '{}' ) );
    JsHelper::PushFile( 'js://vt/edit.js' );
?>
<? if ( !empty( $errors["fatal"] ) ) { ?>
<h4 class="_error"><?= T( 'errors.fatal.%s', $errors['fatal'] ); ?></h4>
<? } ?>
<div class="tabs">
    <?= FormHelper::FormHidden( 'selectedTab', !empty( $selectedTab ) ? $selectedTab : 0, 'selectedTab' ); ?>
    <div class="tabs_head">
        <ul>
            <li><span>{lang:vt.common.commonInfo}</span></li>
        </ul>
    </div>
    <div class="tabs_cont fsMedium">
    	<div class="row _fluid _p" data-row="login">
            <div class="col2 required"><label for="login" class="blockLabel">{lang:vt.user.login}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[login]', $object->login, 'login' ); ?></div>
        </div>
        <div class="row _fluid _p" data-row="password">
            <div class="col2"><label for="password" class="blockLabel">{lang:vt.user.password}</label></div>
            <div class="col6"><?= FormHelper::FormPassword( 'password', $password, 'password', null ) ?></div>
        </div>
    	<div class="row _fluid _p" data-row="statusId">
            <div class="col2 required"><label for="statusId" class="blockLabel">{lang:vt.user.statusId}</label></div>
            <div class="col6"><?= FormHelper::FormSelect( $prefix . '[statusId]', StatusUtility::$Common[$__currentLang], '', '', $object->statusId, null, null, false ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="lastActivityAt">
            <div class="col2"><label for="lastActivityAt" class="blockLabel">{lang:vt.user.lastActivityAt}</label></div>
            <div class="col6"><?= FormHelper::FormDateTime( $prefix . '[lastActivityAt]', $object->lastActivityAt, 'd.m.Y G:i' ); ?></div>
        </div>
    </div>
</div>

