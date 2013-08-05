<?php
    /** @var Album $object */

    $prefix = "album";

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
        <div data-row="title" class="row required">
            <label>{lang:vt.album.title}</label>
            <?= FormHelper::FormInput( $prefix . '[title]', $object->title, 'title', null, array( 'size' => 80 ) ); ?>
        </div>
        <div data-row="description" class="row">
            <label>{lang:vt.album.description}</label>
            <?= FormHelper::FormTextArea( $prefix . '[description]', $object->description, 'description', null, array( 'rows' => 5, 'cols' => 80 ) ); ?>
        </div>
        <div data-row="alias" class="row required">
            <label>{lang:vt.album.alias}</label>
            <?= FormHelper::FormInput( $prefix . '[alias]', $object->alias, 'alias', null, array( 'size' => 80 ) ); ?>
        </div>
        <div data-row="isPrivate" class="row">
            <label>{lang:vt.album.isPrivate}</label>
            <?= FormHelper::FormCheckBox( $prefix . '[isPrivate]', null, 'isPrivate', null, $object->isPrivate ); ?>
        </div>
        <div data-row="startDate" class="row required">
            <label>{lang:vt.album.startDate}</label>
            <?= FormHelper::FormDate( $prefix . '[startDate]', $object->startDate, 'd.m.Y' ); ?>
        </div>
        <div data-row="endDate" class="row">
            <label>{lang:vt.album.endDate}</label>
            <?= FormHelper::FormDate( $prefix . '[endDate]', $object->endDate, 'd.m.Y' ); ?>
        </div>
        <div data-row="orderNumber" class="row inline">
            <label>{lang:vt.album.orderNumber}</label>
            <?= FormHelper::FormInput( $prefix . '[orderNumber]', $object->orderNumber, 'orderNumber', null, array( 'size' => 3 ) ); ?>
        </div>
        <div data-row="folderPath" class="row required">
            <label>{lang:vt.album.folderPath}</label>
            <?= FormHelper::FormInput( $prefix . '[folderPath]', $object->folderPath, 'folderPath', null, array( 'size' => 80, 'readonly' => 'readonly' ) ); ?>
        </div>
        <div data-row="roSecret" class="row required">
            <label>{lang:vt.album.roSecret}</label>
            <?= FormHelper::FormInput( $prefix . '[roSecret]', $object->roSecret, 'roSecret', null, array( 'size' => 80 ) ); ?>
        </div>
        <div data-row="roSecretHd" class="row">
            <label>{lang:vt.album.roSecretHd}</label>
            <?= FormHelper::FormInput( $prefix . '[roSecretHd]', $object->roSecretHd, 'roSecretHd', null, array( 'size' => 80 ) ); ?>
        </div>
        <div data-row="deleteOriginalsAfter" class="row inline">
            <label>{lang:vt.album.deleteOriginalsAfter}</label>
            <?= FormHelper::FormInput( $prefix . '[deleteOriginalsAfter]', $object->deleteOriginalsAfter, 'deleteOriginalsAfter', null, array( 'size' => 3 ) ); ?>
        </div>
        <div data-row="isDescSort" class="row required">
            <label>{lang:vt.album.isDescSort}</label>
            <?= FormHelper::FormCheckBox( $prefix . '[isDescSort]', null, 'isDescSort', null, $object->isDescSort ); ?>
        </div>
        <div data-row="userId" class="row required">
            <label>{lang:vt.album.userId}</label>
            <?= FormHelper::FormSelect( $prefix . '[userId]', $users, "userId", "login", $object->userId, null, null, false ); ?>
        </div>
        <div data-row="statusId" class="row required">
            <label>{lang:vt.album.statusId}</label>
            <?= FormHelper::FormSelect( $prefix . '[statusId]', StatusUtility::$Album[$__currentLang], "", "", $object->statusId, null, null, false ); ?>
        </div>
	</div>
</div>
<script type="text/javascript">
	var jsonErrors = {$jsonErrors};
</script>
 