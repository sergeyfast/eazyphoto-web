<?php
    /** @var NavigationType $object */

    $__pageTitle = LocaleLoader::Translate( "vt.screens.navigationType.editTitle");
	
    $grid = array(
        "basepath"   => Site::GetWebPath( "vt://navigations/types/" )
        , "deleteStr"  => LocaleLoader::Translate( "vt.navigationType.deleteString")
    );
	
	$__breadcrumbs = array( 
		array( 'link' => Site::GetWebPath( "vt://navigations/types/" ) , 'title' => LocaleLoader::Translate( "vt.screens.navigationType.list" ) )
		, array( 'link' => Site::GetWebPath( "vt://navigations/types/edit/" . $objectId ) , 'title' => LocaleLoader::Translate( "vt.common.crumbEdit" ) ) 
	);
?>
{increal:tmpl://vt/header.tmpl.php}
<script type="text/javascript">
    var objectDeleteStr = '{$grid[deleteStr]}';
    var objectBasePath = '{$grid[basepath]}';
</script>
<div class="main">
	<div class="inner">
		<form method="post" action="" enctype="multipart/form-data" data-object-id="{$objectId}" id="data-form">
			{increal:tmpl://vt/elements/menu/breadcrumbs.tmpl.php}
			<div class="pagetitle">
				<div class="controls">
					<a href="#" class="big-delete delete-object-return">{lang:vt.common.delete}</a>
				</div>
				<h1>{$__pageTitle}</h1>
			</div>
			
			<?= FormHelper::FormHidden( 'action', BaseSaveAction::UpdateAction ); ?>
			<?= FormHelper::FormHidden( 'redirect', '', 'redirect' ); ?>
			
			{increal:tmpl://vt/navigations/types/data.tmpl.php}
			
			<div class="buttons">
				<a href="{web:vt://navigations/types/}" class="back">&larr; {lang:vt.common.back}</a>
				<div class="buttons-inner">
					<?= FormHelper::FormSubmit( 'edit', LocaleLoader::Translate( 'vt.common.saveChanges' ), null, 'large' ); ?>
					<?= FormHelper::FormSubmit( 'editPreview', LocaleLoader::Translate( 'vt.common.editPreview' ), '', 'large gray edit-preview' ); ?>
				</div>
			</div>
		</form>
	</div>
</div>
{increal:tmpl://vt/footer.tmpl.php}