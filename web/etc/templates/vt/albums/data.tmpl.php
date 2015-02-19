<?php
    /** @var Album $object */
    /** @var User[] $users */
    

    use Eaze\Helpers\FormHelper;
    use Eaze\Helpers\JsHelper;

    $prefix = 'album';

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
    	<div class="row _fluid _p" data-row="title">
            <div class="col2 required"><label for="title" class="blockLabel">{lang:vt.album.title}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[title]', $object->title, 'title' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="description">
            <div class="col2"><label for="description" class="blockLabel">{lang:vt.album.description}</label></div>
            <div class="col6"><?= FormHelper::FormTextArea( $prefix . '[description]', $object->description, 'description', null, [ 'rows' => 5, 'cols' => 80 ] ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="alias">
            <div class="col2 required"><label for="alias" class="blockLabel">{lang:vt.album.alias}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[alias]', $object->alias, 'alias' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="isPrivate">
            <div class="col2"><label for="isPrivate" class="blockLabel">{lang:vt.album.isPrivate}</label></div>
            <div class="col6"><?= FormHelper::FormCheckbox( $prefix . '[isPrivate]', null, 'isPrivate', null, $object->isPrivate ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="startDate">
            <div class="col2 required"><label for="startDate" class="blockLabel">{lang:vt.album.startDate}</label></div>
            <div class="col6"><?= FormHelper::FormDate( $prefix . '[startDate]', $object->startDate, 'd.m.Y' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="endDate">
            <div class="col2"><label for="endDate" class="blockLabel">{lang:vt.album.endDate}</label></div>
            <div class="col6"><?= FormHelper::FormDate( $prefix . '[endDate]', $object->endDate, 'd.m.Y' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="orderNumber">
            <div class="col2"><label for="orderNumber" class="blockLabel">{lang:vt.album.orderNumber}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[orderNumber]', $object->orderNumber, 'orderNumber' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="folderPath">
            <div class="col2 required"><label for="folderPath" class="blockLabel">{lang:vt.album.folderPath}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[folderPath]', $object->folderPath, 'folderPath' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="roSecret">
            <div class="col2 required"><label for="roSecret" class="blockLabel">{lang:vt.album.roSecret}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[roSecret]', $object->roSecret, 'roSecret' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="roSecretHd">
            <div class="col2"><label for="roSecretHd" class="blockLabel">{lang:vt.album.roSecretHd}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[roSecretHd]', $object->roSecretHd, 'roSecretHd' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="deleteOriginalsAfter">
            <div class="col2"><label for="deleteOriginalsAfter" class="blockLabel">{lang:vt.album.deleteOriginalsAfter}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[deleteOriginalsAfter]', $object->deleteOriginalsAfter, 'deleteOriginalsAfter' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="isDescSort">
            <div class="col2"><label for="isDescSort" class="blockLabel">{lang:vt.album.isDescSort}</label></div>
            <div class="col6"><?= FormHelper::FormCheckbox( $prefix . '[isDescSort]', null, 'isDescSort', null, $object->isDescSort ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="userId">
            <div class="col2 required"><label for="userId" class="blockLabel">{lang:vt.album.userId}</label></div>
            <div class="col6"><?= FormHelper::FormSelect( $prefix . '[userId]', $users, 'userId', 'login', $object->userId, null, null, false ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="statusId">
            <div class="col2 required"><label for="statusId" class="blockLabel">{lang:vt.album.statusId}</label></div>
            <div class="col6"><?= FormHelper::FormSelect( $prefix . '[statusId]', StatusUtility::$Common[$__currentLang], '', '', $object->statusId, null, null, false ); ?></div>
        </div>
    </div>
</div>

