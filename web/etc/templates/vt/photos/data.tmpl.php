<?php
    /** @var Photo $object */

    $prefix = "photo";

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
        <div data-row="albumId" class="row required">
            <label>{lang:vt.photo.albumId}</label>
            <?= FormHelper::FormSelect( $prefix . '[albumId]', $albums, "albumId", "title", $object->albumId, null, null, false ); ?>
        </div>
        <div data-row="originalName" class="row required">
            <label>{lang:vt.photo.originalName}</label>
            <?= FormHelper::FormInput( $prefix . '[originalName]', $object->originalName, 'originalName', null, array( 'size' => 80 ) ); ?>
        </div>
        <div data-row="filename" class="row required">
            <label>{lang:vt.photo.filename}</label>
            <?= FormHelper::FormInput( $prefix . '[filename]', $object->filename, 'filename', null, array( 'size' => 80 ) ); ?>
        </div>
        <div data-row="fileSize" class="row required">
            <label>{lang:vt.photo.fileSize}</label>
            <?= FormHelper::FormInput( $prefix . '[fileSize]', $object->fileSize, 'fileSize', null, array( 'size' => 80 ) ); ?>
        </div>
        <div data-row="fileSizeHd" class="row required">
            <label>{lang:vt.photo.fileSizeHd}</label>
            <?= FormHelper::FormInput( $prefix . '[fileSizeHd]', $object->fileSizeHd, 'fileSizeHd', null, array( 'size' => 80 ) ); ?>
        </div>
        <div data-row="orderNumber" class="row">
            <label>{lang:vt.photo.orderNumber}</label>
            <?= FormHelper::FormInput( $prefix . '[orderNumber]', $object->orderNumber, 'orderNumber', null, array( 'size' => 80 ) ); ?>
        </div>
        <div data-row="afterText" class="row">
            <label>{lang:vt.photo.afterText}</label>
            <?= FormHelper::FormTextArea( $prefix . '[afterText]', $object->afterText, 'afterText', null, array( 'rows' => 5, 'cols' => 80 ) ); ?>
        </div>
        <div data-row="title" class="row">
            <label>{lang:vt.photo.title}</label>
            <?= FormHelper::FormInput( $prefix . '[title]', $object->title, 'title', null, array( 'size' => 80 ) ); ?>
        </div>
        <div data-row="createdAt" class="row">
            <label>{lang:vt.photo.createdAt}</label>
            <?= FormHelper::FormDateTime( $prefix . '[createdAt]', $object->createdAt, 'd.m.Y G:i' ); ?>
        </div>
        <div data-row="photoDate" class="row">
            <label>{lang:vt.photo.photoDate}</label>
            <?= FormHelper::FormDateTime( $prefix . '[photoDate]', $object->photoDate, 'd.m.Y G:i' ); ?>
        </div>
        <div data-row="statusId" class="row required">
            <label>{lang:vt.photo.statusId}</label>
            <?= FormHelper::FormSelect( $prefix . '[statusId]', StatusUtility::$Common[$__currentLang], "", "", $object->statusId, null, null, false ); ?>
        </div>
	</div>
</div>
<script type="text/javascript">
	var jsonErrors = {$jsonErrors};
</script>
 