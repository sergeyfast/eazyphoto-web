<?php
    /** @var Navigation[] $list */

    $__pageTitle = LocaleLoader::Translate( "vt.screens.navigation.list");

    $grid = array(
        "columns" => array(
           LocaleLoader::Translate( "vt.navigation.title" )
            , LocaleLoader::Translate( "vt.navigation.orderNumber" )
            , LocaleLoader::Translate( "vt.navigation.navigationTypeId" )
            , LocaleLoader::Translate( "vt.navigation.url" )
            , LocaleLoader::Translate( "vt.navigation.staticPageId" )
            , LocaleLoader::Translate( "vt.navigation.statusId" )
        )
        , "colspans"	=> array()
        , "sorts"		=> array(0 => "title", 1 => "orderNumber", 2 => "navigationType.title", 3 => "url", 4 => "staticPage.title", 5 => "statusId")
        , "operations"	=> true
        , "allowAdd"	=> true
        , "canPages"	=> NavigationFactory::CanPages()
        , "basepath"	=> Site::GetWebPath( "vt://navigations/" )
        , "addpath"		=> Site::GetWebPath( "vt://navigations/add" )
        , "title"		=> $__pageTitle
		, "description"	=> ''
        , "pageSize"	=> HtmlHelper::RenderToForm( $search["pageSize"] )
        , "deleteStr"	=> LocaleLoader::Translate( "vt.navigation.deleteString")
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
                    <label>{lang:vt.navigation.title}</label>
                    <?= FormHelper::FormInput( "search[title]", $search['title'], 'title', null, array( 'size' => 80 ) ); ?>
                </div>
                <div class="row">
                    <label>{lang:vt.navigation.navigationTypeId}</label>
                    <?= FormHelper::FormSelect( "search[navigationTypeId]", $navigationTypes, "navigationTypeId", "title", $search['navigationTypeId'], null, null, true ); ?>
                </div>
                <div class="row">
                    <label>{lang:vt.navigation.statusId}</label>
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
        $id         = $object->navigationId;
        $editpath   = $grid['basepath'] . "edit/" . $id;
?>
			<tr data-object-id="{$id}">
                <td class="header">{$object.title}</td>
                <td>{$object.orderNumber}</td>
                <td>{$object.navigationType.title}</td>
                <td>
                    <? if (!empty($object->url)) { ?>
                    <span class="status blue" title="{form:$object->url}">{lang:vt.common.yes}</span>
                    <? } ?>
                </td>
                <td><?= !empty($object->staticPage) ? $object->staticPage->title : "" ?></td>
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