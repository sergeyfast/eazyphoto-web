<?php
    /** @var StaticPage $object */
    /** @var StaticPage[] $staticPages */
    

    use Eaze\Helpers\FormHelper;
    use Eaze\Helpers\JsHelper;

    $prefix = 'staticPage';

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
            <li><span>Картинки</span></li>
            <li><span>Мета</span></li>
        </ul>
    </div>
    <div class="tabs_cont fsMedium">
    	<div class="row _fluid _p" data-row="title">
            <div class="col2 required"><label for="title" class="blockLabel">{lang:vt.staticPage.title}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[title]', $object->title, 'title' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="url">
            <div class="col2 required"><label for="url" class="blockLabel">{lang:vt.staticPage.url}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[url]', $object->url, 'url' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="content">
            <div class="col2"><label for="content" class="blockLabel">{lang:vt.staticPage.content}</label></div>
            <div class="col6"><?= FormHelper::FormEditor( $prefix . '[content]', $object->content, 'content', null, [ 'rows' => 5, 'cols' => 80 ] ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="orderNumber">
            <div class="col2"><label for="orderNumber" class="blockLabel">{lang:vt.staticPage.orderNumber}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[orderNumber]', $object->orderNumber, 'orderNumber' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="parentStaticPageId">
            <div class="col2"><label for="parentStaticPageId" class="blockLabel">{lang:vt.staticPage.parentStaticPageId}</label></div>
            <div class="col6"><?= StaticPageHelper::FormSelect( $prefix . '[parentStaticPageId]', $staticPages, $object->parentStaticPageId, false ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="statusId">
            <div class="col2 required"><label for="statusId" class="blockLabel">{lang:vt.staticPage.statusId}</label></div>
            <div class="col6"><?= FormHelper::FormSelect( $prefix . '[statusId]', StatusUtility::$Common[$__currentLang], '', '', $object->statusId, null, null, false ); ?></div>
        </div>
    </div>
    {increal:tmpl://vt/elements/images.tmpl.php}
    {increal:tmpl://vt/elements/meta.tmpl.php}
</div>
<? $__useEditor = true; ?>
