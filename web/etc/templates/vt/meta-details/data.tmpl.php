<?php
    /** @var MetaDetail $object */
    

    use Eaze\Helpers\FormHelper;
    use Eaze\Helpers\JsHelper;

    $prefix = 'metaDetail';

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
    	<div class="row _fluid _p" data-row="objectId">
            <div class="col2"><label for="objectId" class="blockLabel">{lang:vt.metaDetail.objectId}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[objectId]', $object->objectId, 'objectId' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="objectClass">
            <div class="col2"><label for="objectClass" class="blockLabel">{lang:vt.metaDetail.objectClass}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[objectClass]', $object->objectClass, 'objectClass' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="url">
            <div class="col2"><label for="url" class="blockLabel">{lang:vt.metaDetail.url}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[url]', $object->url, 'url' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="pageTitle">
            <div class="col2"><label for="pageTitle" class="blockLabel">{lang:vt.metaDetail.pageTitle}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[pageTitle]', $object->pageTitle, 'pageTitle' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="metaKeywords">
            <div class="col2"><label for="metaKeywords" class="blockLabel">{lang:vt.metaDetail.metaKeywords}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[metaKeywords]', $object->metaKeywords, 'metaKeywords' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="metaDescription">
            <div class="col2"><label for="metaDescription" class="blockLabel">{lang:vt.metaDetail.metaDescription}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[metaDescription]', $object->metaDescription, 'metaDescription' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="alt">
            <div class="col2"><label for="alt" class="blockLabel">{lang:vt.metaDetail.alt}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[alt]', $object->alt, 'alt' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="canonicalUrl">
            <div class="col2"><label for="canonicalUrl" class="blockLabel">{lang:vt.metaDetail.canonicalUrl}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[canonicalUrl]', $object->canonicalUrl, 'canonicalUrl' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="statusId">
            <div class="col2 required"><label for="statusId" class="blockLabel">{lang:vt.metaDetail.statusId}</label></div>
            <div class="col6"><?= FormHelper::FormSelect( $prefix . '[statusId]', StatusUtility::$Common[$__currentLang], '', '', $object->statusId, null, null, false ); ?></div>
        </div>
    </div>
</div>

