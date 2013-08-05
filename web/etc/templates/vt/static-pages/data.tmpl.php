<?php
    /** @var StaticPage $object */

    $prefix = "staticPage";

    if ( empty( $errors ) ) $errors = array();
	if ( empty( $jsonErrors ) ) $jsonErrors = '{}';

    if ( !empty($errors["fatal"] ) ) {
		?><h3 class="error"><?= LocaleLoader::Translate( 'errors.fatal.' . $errors["fatal"] ); ?></h3><?
	}
?>
<div class="tabs">
    <?= FormHelper::FormHidden( 'selectedTab', !empty( $selectedTab ) ? $selectedTab : 0, 'selectedTab' ); ?>
    <ul class="tabs-list">
        <li><a href="#page-0">{lang:vt.staticPage.commonInfo}</a></li>
        <li><a href="#page-1">{lang:vt.staticPage.metaDetails}</a></li>
    </ul>

    <div id="page-0" class="tab-page rows">
        <div data-row="title" class="row required">
            <label>{lang:vt.staticPage.title}</label>
            <?= FormHelper::FormInput( $prefix . '[title]', $object->title, 'title', null, array( 'size' => 80 ) ); ?>
        </div>
        <div data-row="url" class="row required">
            <label>{lang:vt.staticPage.url}</label>
            <?= FormHelper::FormInput( $prefix . '[url]', $object->url, 'url', null, array( 'size' => 80 ) ); ?>
        </div>
        <div data-row="content" class="row">
            <label>{lang:vt.staticPage.content}</label>
            <?= FormHelper::FormEditor( $prefix . '[content]', $object->content, 'content', null, array( 'rows' => 5, 'cols' => 80 ) ); ?>
        </div>
        <div data-row="parentStaticPageId" class="row">
            <label>{lang:vt.staticPage.parentStaticPageId}</label>
            <?= StaticPageHelper::FormSelect( $prefix . '[parentStaticPageId]', $staticPages, $object->parentStaticPageId, false ); ?>
        </div>
        <div data-row="statusId" class="row required">
            <label>{lang:vt.staticPage.statusId}</label>
            <?= FormHelper::FormSelect( $prefix . '[statusId]', StatusUtility::$Common[$__currentLang], "", "", $object->statusId, null, null, false ); ?>
        </div>
    </div>
    <div id="page-1" class="tab-page rows">
        <div data-row="pageTitle" class="row">
            <label>{lang:vt.staticPage.pageTitle}</label>
            <?= FormHelper::FormInput( $prefix . '[pageTitle]', $object->pageTitle, 'pageTitle', null, array( 'size' => 80 ) ); ?>
        </div>
        <div data-row="metaKeywords" class="row">
            <label>{lang:vt.staticPage.metaKeywords}</label>
            <?= FormHelper::FormTextArea( $prefix . '[metaKeywords]', $object->metaKeywords, 'metaKeywords', null, array( 'rows' => 5, 'cols' => 80 ) ); ?>
        </div>
        <div data-row="metaDescription" class="row">
            <label>{lang:vt.staticPage.metaDescription}</label>
            <?= FormHelper::FormTextArea( $prefix . '[metaDescription]', $object->metaDescription, 'metaDescription', null, array( 'rows' => 5, 'cols' => 80 ) ); ?>
        </div>
        <div data-row="orderNumber" class="row">
            <label>{lang:vt.staticPage.orderNumber}</label>
            <?= FormHelper::FormInput( $prefix . '[orderNumber]', $object->orderNumber, 'orderNumber', null, array( 'size' => 80 ) ); ?>
        </div>
	</div>
</div>
<script type="text/javascript">
	var jsonErrors = {$jsonErrors};
</script>
<?php
	$__useEditor = true;
?>
