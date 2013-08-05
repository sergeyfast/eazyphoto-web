<?php
    /** @var Navigation $object */

    $prefix = "navigation";

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
        <div data-row="title" class="row">
            <label>{lang:vt.navigation.title}</label>
            <?= FormHelper::FormInput( $prefix . '[title]', $object->title, 'title', null, array( 'size' => 80 ) ); ?>
        </div>
        <div data-row="orderNumber" class="row required">
            <label>{lang:vt.navigation.orderNumber}</label>
            <?= FormHelper::FormInput( $prefix . '[orderNumber]', $object->orderNumber, 'orderNumber', null, array( 'size' => 80 ) ); ?>
        </div>
        <div data-row="navigationTypeId" class="row required">
            <label>{lang:vt.navigation.navigationTypeId}</label>
            <?= FormHelper::FormSelect( $prefix . '[navigationTypeId]', $navigationTypes, "navigationTypeId", "title", $object->navigationTypeId, null, null, false ); ?>
        </div>
        <div data-row="url" class="row">
            <label>{lang:vt.navigation.url}</label>
            <?= FormHelper::FormInput( $prefix . '[url]', $object->url, 'url', null, array( 'size' => 80 ) ); ?>
        </div>
        <div data-row="staticPageId" class="row">
            <label>{lang:vt.navigation.staticPageId}</label>
            <?= StaticPageHelper::FormSelect( $prefix . '[staticPageId]', $staticPages, $object->staticPageId, false ); ?>
        </div>
        <div data-row="statusId" class="row required">
            <label>{lang:vt.navigation.statusId}</label>
            <?= FormHelper::FormSelect( $prefix . '[statusId]', StatusUtility::$Common[$__currentLang], "", "", $object->statusId, null, null, false ); ?>
        </div>
	</div>
</div>
<script type="text/javascript">
	var jsonErrors = {$jsonErrors};
</script>
 