<?php
    /** @var Photo $object */
    /** @var Album[] $albums */
    

    use Eaze\Helpers\FormHelper;
    use Eaze\Helpers\JsHelper;

    $prefix = 'photo';

    if ( empty( $errors ) ) {
        $errors = [];
    }

    JsHelper::PushLine( sprintf( 'var jsonErrors = %s;', !empty( $jsonErrors ) ? $jsonErrors : '{}' ) );
    JsHelper::PushFile( 'js://vt/edit.js' );
?>
<? if ( !empty( $errors['fatal'] ) ) { ?><h4 class="cWarn"><?= T( 'errors.fatal.' . $errors['fatal'] ); ?></h4><? } ?>
<div class="tabs">
    <?= FormHelper::FormHidden( 'selectedTab', !empty( $selectedTab ) ? $selectedTab : 0, 'selectedTab' ); ?>
    <div class="tabs_head">
        <ul>
            <li><span>{lang:vt.common.commonInfo}</span></li>
            <? if ( $object->exif ) { ?><li><span>EXIF</span></li><? } ?>
        </ul>
    </div>
    <div class="tabs_cont fsMedium">
    	<div class="row _fluid _p" data-row="albumId">
            <div class="col2 required"><label for="albumId" class="blockLabel">{lang:vt.photo.albumId}</label></div>
            <div class="col6"><?= FormHelper::FormSelect( $prefix . '[albumId]', $albums, 'albumId', 'title', $object->albumId, null, 'select2', false ); ?></div>
        </div>
        <div class="row _fluid _p" data-row="afterText">
            <div class="col2"><label for="afterText" class="blockLabel">{lang:vt.photo.afterText}</label></div>
            <div class="col6"><?= FormHelper::FormTextArea( $prefix . '[afterText]', $object->afterText, 'afterText', null, [ 'rows' => 5, 'cols' => 80 ] ); ?></div>
        </div>
        <div class="row _fluid _p" data-row="title">
            <div class="col2"><label for="title" class="blockLabel">{lang:vt.photo.title}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[title]', $object->title, 'title' ); ?></div>
        </div>
        <div class="row _fluid _p" data-row="orderNumber">
            <div class="col2"><label for="orderNumber" class="blockLabel">{lang:vt.photo.orderNumber}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[orderNumber]', $object->orderNumber, 'orderNumber' ); ?></div>
        </div>
        <div class="row _fluid _p" data-row="isFavorite">
            <div class="col6 offset2"><?= FormHelper::FormCheckbox( $prefix . '[isFavorite]', null, 'isFavorite', null, $object->isFavorite ); ?> <label for="isFavorite" class="blockLabel">{lang:vt.photo.isFavorite}</label></div>
        </div>
        <h3>Параметры файла</h3>
    	<div class="row _fluid _p" data-row="originalName">
            <div class="col2 required"><label for="originalName" class="blockLabel">{lang:vt.photo.originalName}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[originalName]', $object->originalName, 'originalName' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="filename">
            <div class="col2 required"><label for="filename" class="blockLabel">{lang:vt.photo.filename}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[filename]', $object->filename, 'filename' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="fileSize">
            <div class="col2 required"><label for="fileSize" class="blockLabel">{lang:vt.photo.fileSize}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[fileSize]', $object->fileSize, 'fileSize' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="createdAt">
            <div class="col2"><label for="createdAt" class="blockLabel">{lang:vt.photo.createdAt}</label></div>
            <div class="col6"><?= FormHelper::FormDateTime( $prefix . '[createdAt]', $object->createdAt, 'd.m.Y G:i' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="photoDate">
            <div class="col2"><label for="photoDate" class="blockLabel">{lang:vt.photo.photoDate}</label></div>
            <div class="col6"><?= FormHelper::FormDateTime( $prefix . '[photoDate]', $object->photoDate, 'd.m.Y G:i' ); ?></div>
        </div>
        <div class="row _fluid _p" data-row="fileSizeHd">
            <div class="col2 required"><label for="fileSizeHd" class="blockLabel">{lang:vt.photo.fileSizeHd}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[fileSizeHd]', $object->fileSizeHd, 'fileSizeHd' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="statusId">
            <div class="col2 required"><label for="statusId" class="blockLabel">{lang:vt.photo.statusId}</label></div>
            <div class="col6"><?= FormHelper::FormSelect( $prefix . '[statusId]', StatusUtility::$Common[$__currentLang], '', '', $object->statusId, null, null, false ); ?></div>
        </div>
    </div>
    <? if ( $object->exif ) { ?>
    <div class="tabs_cont fsMedium">
        <? foreach( $object->exif as $k => $v ) { ?>
        <div class="row _fluid">
            <div class="col3">{$k}</div>
            <div class="col6">
            <? if ( is_array( $v ) && !array_key_exists( 0, $v ) ) { ?>
                <? foreach( $v as $vk => $vv ) { ?>
                    <div class="row _fluid">
                        <div class="col5">{$vk}</div>
                        <div class="col7"><?= is_array( $vv ) ? implode( ', ', $vv ) : $vv ?></div>
                    </div>
                <? } ?>
            <? } else { ?><?= is_array( $v ) ? implode( ', ', $v ) : $v ?><? } ?>
            </div>
        </div>
        <? } ?>
    </div>
    <? } ?>
</div>