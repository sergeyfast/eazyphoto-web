<?php
    /** @var Tag $object */
    /** @var Tag[] $parentTags */
    

    use Eaze\Helpers\FormHelper;
    use Eaze\Helpers\JsHelper;

    $prefix = 'tag';

    if ( empty( $errors ) ) {
        $errors = [];
    }

    JsHelper::PushLine( sprintf( 'var jsonErrors = %s;', !empty( $jsonErrors ) ? $jsonErrors : '{}' ) );
    JsHelper::PushFiles( [ 'js://vt/edit.js', 'js://vt/translit-alias.js' ] );
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
            <div class="col2 required"><label for="title" class="blockLabel">{lang:vt.tag.title}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[title]', $object->title, 'title', null, [ 'placeholder' => 'Название должно быть уникальным'] ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="alias">
            <div class="col2 required"><label for="alias" class="blockLabel">{lang:vt.tag.alias}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[alias]', $object->alias, 'alias' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="description">
            <div class="col2"><label for="description" class="blockLabel">{lang:vt.tag.description}</label></div>
            <div class="col6"><?= FormHelper::FormTextArea( $prefix . '[description]', $object->description, 'description', null, [ 'rows' => 5, 'cols' => 80 ] ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="orderNumber">
            <div class="col2"><label for="orderNumber" class="blockLabel">{lang:vt.tag.orderNumber}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[orderNumber]', $object->orderNumber, 'orderNumber', null, [ 'placeholder' => 'Если поле заполнено, то тег отображается на главной'] ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="photo.title">
            <div class="col2"><label for="photo.title" class="blockLabel">{lang:vt.tag.photoId}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[photoId]', $object->photoId, null, null, [ 'placeholder' => 'Введите номер фотографии для создания обложки тега'] ); ?></div>
            <? if ( $object->photoPath ) { ?><div class="col2"><img src="{web:$object.photoPath}" width="40" /></div><? } ?>
        </div>
    	<div class="row _fluid _p" data-row="parentTagId">
            <div class="col2"><label for="parentTagId" class="blockLabel">{lang:vt.tag.parentTagId}</label></div>
            <div class="col6"><?= FormHelper::FormSelect( $prefix . '[parentTagId]', $parentTags, 'tagId', 'title', $object->parentTagId, null, 'select2', true ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="statusId">
            <div class="col2 required"><label for="statusId" class="blockLabel">{lang:vt.tag.statusId}</label></div>
            <div class="col6"><?= FormHelper::FormSelect( $prefix . '[statusId]', StatusUtility::$Common[$__currentLang], '', '', $object->statusId, null, null, false ); ?></div>
        </div>
    </div>
</div>
