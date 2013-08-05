<?php
    /** @var Navigation $object */

    $__pageTitle = LocaleLoader::Translate( "vt.screens.navigation.addTitle");
	
	$__breadcrumbs = array( 
		array( 'link' => Site::GetWebPath( "vt://navigations/" ) , 'title' => LocaleLoader::Translate( "vt.screens.navigation.list" ) )
		, array( 'link' => Site::GetWebPath( "vt://navigations/add" ) , 'title' => LocaleLoader::Translate( "vt.common.crumbAdd" ) ) 
	);
?>
{increal:tmpl://vt/header.tmpl.php}
<div class="main">
	<div class="inner">
		<form method="post" action="" enctype="multipart/form-data" id="data-form">
			{increal:tmpl://vt/elements/menu/breadcrumbs.tmpl.php}
			<div class="pagetitle">
				<h1>{$__pageTitle}</h1>
			</div>
			
			<?= FormHelper::FormHidden( 'action', BaseSaveAction::AddAction ); ?>
			
			{increal:tmpl://vt/navigations/data.tmpl.php}
			
			<div class="buttons">
				<a href="{web:vt://navigations/}" class="back">&larr; {lang:vt.common.back}</a>
				<div class="buttons-inner">
					<?= FormHelper::FormSubmit( 'add', LocaleLoader::Translate( 'vt.common.saveChanges' ), null, 'large' ); ?>
				</div>
			</div>
		</form>
	</div>
</div>
{increal:tmpl://vt/footer.tmpl.php}