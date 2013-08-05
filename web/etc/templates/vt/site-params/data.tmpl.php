<?php
    /** @var SiteParam $object */

    $prefix = "siteParam";

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
        <div data-row="alias" class="row required">
            <label>{lang:vt.siteParam.alias}</label>
            <?= FormHelper::FormInput( $prefix . '[alias]', $object->alias, 'alias', null, array( 'size' => 80 ) ); ?>
        </div>
        <div data-row="value" class="row required">
            <label>{lang:vt.siteParam.value}</label>
            <?= FormHelper::FormInput( $prefix . '[value]', $object->value, 'value', null, array( 'size' => 80 ) ); ?>
        </div>
        <div data-row="description" class="row">
            <label>{lang:vt.siteParam.description}</label>
            <?= FormHelper::FormInput( $prefix . '[description]', $object->description, 'description', null, array( 'size' => 80 ) ); ?>
        </div>
        <div data-row="statusId" class="row required">
            <label>{lang:vt.siteParam.statusId}</label>
            <?= FormHelper::FormSelect( $prefix . '[statusId]', StatusUtility::$Common[$__currentLang], "", "", $object->statusId, null, null, false ); ?>
        </div>
	</div>
</div>
<script type="text/javascript">
	var jsonErrors = {$jsonErrors};
</script>
 