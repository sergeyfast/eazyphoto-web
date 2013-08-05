<?php
    /** @var NavigationType $object */

    $prefix = "navigationType";

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
            <label>{lang:vt.navigationType.title}</label>
            <?= FormHelper::FormInput( $prefix . '[title]', $object->title, 'title', null, array( 'size' => 80 ) ); ?>
        </div>
        <div data-row="alias" class="row required">
            <label>{lang:vt.navigationType.alias}</label>
            <?= FormHelper::FormInput( $prefix . '[alias]', $object->alias, 'alias', null, array( 'size' => 80 ) ); ?>
        </div>
        <div data-row="statusId" class="row required">
            <label>{lang:vt.navigationType.statusId}</label>
            <?= FormHelper::FormSelect( $prefix . '[statusId]', StatusUtility::$Common[$__currentLang], "", "", $object->statusId, null, null, false ); ?>
        </div>
	</div>
</div>
<script type="text/javascript">
	var jsonErrors = {$jsonErrors};
</script>
 