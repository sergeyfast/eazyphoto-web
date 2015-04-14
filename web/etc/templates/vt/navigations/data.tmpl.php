<?php
    /** @var Navigation $object */
    /** @var NavigationType[] $navigationTypes */
    /** @var StaticPage[] $staticPages */
    

    use Eaze\Helpers\FormHelper;
    use Eaze\Helpers\JsHelper;

    $prefix = 'navigation';

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
            <div class="col2"><label for="title" class="blockLabel">{lang:vt.navigation.title}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[title]', $object->title, 'title' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="orderNumber">
            <div class="col2 required"><label for="orderNumber" class="blockLabel">{lang:vt.navigation.orderNumber}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[orderNumber]', $object->orderNumber, 'orderNumber' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="navigationTypeId">
            <div class="col2 required"><label for="navigationTypeId" class="blockLabel">{lang:vt.navigation.navigationTypeId}</label></div>
            <div class="col6"><?= FormHelper::FormSelect( $prefix . '[navigationTypeId]', $navigationTypes, 'navigationTypeId', 'title', $object->navigationTypeId, null, null, false ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="url">
            <div class="col2"><label for="url" class="blockLabel">{lang:vt.navigation.url}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[url]', $object->url, 'url' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="staticPageId">
            <div class="col2"><label for="staticPageId" class="blockLabel">{lang:vt.navigation.staticPageId}</label></div>
            <div class="col6"><?= FormHelper::FormSelect( $prefix . '[staticPageId]', $staticPages, 'staticPageId', 'title', $object->staticPageId, null, null, true ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="statusId">
            <div class="col2 required"><label for="statusId" class="blockLabel">{lang:vt.navigation.statusId}</label></div>
            <div class="col6"><?= FormHelper::FormSelect( $prefix . '[statusId]', StatusUtility::$Common[$__currentLang], '', '', $object->statusId, null, null, false ); ?></div>
        </div>
        <div class="row _fluid _p" data-row="image">
            <div class="col2"><label for="image" class="blockLabel">Картинка</label></div>
            <div class="col6"><?= VfsHelper::FormVfsFilePath( $prefix . '[params][image]', 'image', $object->Image(),  'image' ); ?></div>
        </div>
    </div>
</div>

