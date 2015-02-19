<?php
    /** @var SiteParam $object */
    

    use Eaze\Helpers\FormHelper;
    use Eaze\Helpers\JsHelper;

    $prefix = 'siteParam';

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
    	<div class="row _fluid _p" data-row="alias">
            <div class="col2 required"><label for="alias" class="blockLabel">{lang:vt.siteParam.alias}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[alias]', $object->alias, 'alias' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="value">
            <div class="col2 required"><label for="value" class="blockLabel">{lang:vt.siteParam.value}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[value]', $object->value, 'value' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="description">
            <div class="col2"><label for="description" class="blockLabel">{lang:vt.siteParam.description}</label></div>
            <div class="col6"><?= FormHelper::FormInput( $prefix . '[description]', $object->description, 'description' ); ?></div>
        </div>
    	<div class="row _fluid _p" data-row="statusId">
            <div class="col2 required"><label for="statusId" class="blockLabel">{lang:vt.siteParam.statusId}</label></div>
            <div class="col6"><?= FormHelper::FormSelect( $prefix . '[statusId]', StatusUtility::$Common[$__currentLang], '', '', $object->statusId, null, null, false ); ?></div>
        </div>
    </div>
</div>

