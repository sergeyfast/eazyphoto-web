<?php
    /** @var Photo[] $list */

    $__pageTitle = LocaleLoader::Translate( "vt.screens.photo.list");

    $grid = array(
        "columns" => array(
           LocaleLoader::Translate( "vt.photo.albumId" )
            , LocaleLoader::Translate( "vt.photo.originalName" )
            , LocaleLoader::Translate( "vt.photo.orderNumber" )
            , LocaleLoader::Translate( "vt.photo.photoDate" )
            , LocaleLoader::Translate( "vt.photo.statusId" )
        )
        , "colspans"	=> array( 0 => 2 )
        , "sorts"		=> array(0 => "album.title", 1 => "originalName", 2 => "orderNumber", 3 => "photoDate", 4 => "statusId")
        , "operations"	=> true
        , "allowAdd"	=> true
        , "canPages"	=> PhotoFactory::CanPages()
        , "basepath"	=> Site::GetWebPath( "vt://photos/" )
        , "addpath"		=> Site::GetWebPath( "vt://photos/add" )
        , "title"		=> $__pageTitle
		, "description"	=> ''
        , "pageSize"	=> HtmlHelper::RenderToForm( $search["pageSize"] )
        , "deleteStr"	=> LocaleLoader::Translate( "vt.photo.deleteString")
    );
	
	$__breadcrumbs = array( array( 'link' => $grid['basepath'], 'title' => $__pageTitle ) );
?>
{increal:tmpl://vt/header.tmpl.php}
<div class="main">
	<div class="inner">
		{increal:tmpl://vt/elements/menu/breadcrumbs.tmpl.php}
		<div class="pagetitle">
			<? if( $grid['allowAdd'] ) { ?>
			<div class="controls"><a href="{$grid[addpath]}" class="add"><span>{lang:vt.common.add}</span></a></div>
			<? } ?>
			<h1>{$__pageTitle}</h1>
		</div>
		{$grid[description]}
		<div class="search<?= $hideSearch == "true" ? " closed" : ""  ?>">
			<a href="#" class="search-close"><span>{lang:vt.common.closeSearch}</span></a>
			<a href="#" class="search-open"><span>{lang:vt.common.openSearch}</span></a>
			<form class="search-form" id="searchForm" method="post" action="{$grid[basepath]}">
				<input type="hidden" value="1" name="searchForm" />
				<input type="hidden" value="" id="pageId" name="page" />
				<input type="hidden" value="{$grid[pageSize]}" id="pageSize" name="search[pageSize]" />
				<input type="hidden" value="{form:$sortField}" id="sortField" name="sortField" />
				<input type="hidden" value="{form:$sortType}" id="sortType" name="sortType" />
                <div class="row">
                    <label>{lang:vt.photo.albumId}</label>
                    <?= FormHelper::FormSelect( "search[albumId]", $albums, "albumId", "title", $search['albumId'], null, null, true ); ?>
                </div>
                <div class="row">
                    <label>{lang:vt.photo.originalName}</label>
                    <?= FormHelper::FormInput( "search[originalName]", $search['originalName'], 'originalName', null, array( 'size' => 80 ) ); ?>
                </div>
                <div class="row">
                    <label>{lang:vt.photo.orderNumber}</label>
                    <?= FormHelper::FormInput( "search[orderNumber]", $search['orderNumber'], 'orderNumber', null, array( 'size' => 80 ) ); ?>
                </div>
                <div class="row">
                    <label>{lang:vt.photo.afterText}</label>
                    <?= FormHelper::FormInput( "search[afterText]", $search['afterText'], 'afterText', null, array( 'size' => 80 ) ); ?>
                </div>
                <div class="row">
                    <label>{lang:vt.photo.title}</label>
                    <?= FormHelper::FormInput( "search[title]", $search['title'], 'title', null, array( 'size' => 80 ) ); ?>
                </div>
                <div class="row">
                    <label>{lang:vt.photo.statusId}</label>
                    <?= FormHelper::FormSelect( "search[statusId]", StatusUtility::$Common[$__currentLang], "", "", $search['statusId'], null, null, true ); ?>
                </div>
				<input type="submit" value="{lang:vt.common.find}" />
			</form>
		</div>
		
		<!-- GRID -->
		{increal:tmpl://vt/elements/datagrid/header.tmpl.php}
<?php
    $langEdit   = LocaleLoader::Translate( "vt.common.edit" );
    $langDelete = LocaleLoader::Translate( "vt.common.delete" );

    foreach ( $list as $object )  {
        $id         = $object->photoId;
        $editpath   = $grid['basepath'] . "edit/" . $id;
?>
			<tr data-object-id="{$id}">
                <td><img src="<?= LinkUtility::GetPhotoThumb( $object, true ) ?>" width="30"></td>
                <td>{$object.album.title}</td>
                <td>{$object.originalName}</td>
                <td>{$object.orderNumber}</td>
                <td><?= ( !empty( $object->photoDate ) ? $object->photoDate->DefaultFormat() : '' ) ?></td>
                <td><?= StatusUtility::GetStatusTemplate($object->statusId) ?></td>
				<td width="10%">
					<ul class="actions">
						<li class="edit"><a href="{$editpath}" title="{$langEdit}">{$langEdit}</a></li><li class="delete"><a href="#" class="delete-object" title="{$langDelete}">{$langDelete}</a></li>
					</ul>
				</td>
	        </tr>
<?php
    }
?>
		{increal:tmpl://vt/elements/datagrid/footer.tmpl.php}
		<!-- EOF GRID -->
	</div>
</div>
{increal:tmpl://vt/footer.tmpl.php}