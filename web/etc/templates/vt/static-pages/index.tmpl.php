<?php
    /** @var StaticPage[] $list */

    $__pageTitle = LocaleLoader::Translate( "vt.screens.staticPage.list");

    $grid = array(
        "columns" => array(
           LocaleLoader::Translate( "vt.staticPage.title" )
            , LocaleLoader::Translate( "vt.staticPage.url" )
            , LocaleLoader::Translate( "vt.staticPage.orderNumber" )
            , LocaleLoader::Translate( "vt.staticPage.parentStaticPageId" )
            , LocaleLoader::Translate( "vt.staticPage.statusId" )
        )
        , "colspans"	=> array()
        , "sorts"		=> array(0 => "title", 1 => "url", 2 => "orderNumber", 3 => "parentStaticPage.title", 4 => "statusId")
        , "operations"	=> true
        , "allowAdd"	=> true
        , "canPages"	=> StaticPageFactory::CanPages()
        , "basepath"	=> Site::GetWebPath( "vt://static-pages/" )
        , "addpath"		=> Site::GetWebPath( "vt://static-pages/add" )
        , "title"		=> $__pageTitle
		, "description"	=> ''
        , "pageSize"	=> HtmlHelper::RenderToForm( $search["pageSize"] )
        , "deleteStr"	=> LocaleLoader::Translate( "vt.staticPage.deleteString")
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
                    <label>{lang:vt.staticPage.title}</label>
                    <?= FormHelper::FormInput( "search[title]", $search['title'], 'title', null, array( 'size' => 80 ) ); ?>
                </div>
                <div class="row">
                    <label>{lang:vt.staticPage.content}</label>
                    <?= FormHelper::FormInput( "search[content]", $search['content'], 'content', null, array( 'size' => 80 ) ); ?>
                </div>
                <div class="row">
                    <label>{lang:vt.staticPage.parentStaticPageId}</label>
                    <?= StaticPageHelper::FormSelect( "search[parentStaticPageId]", $staticPages, $search["parentStaticPageId"] ); ?>
                </div>
                <div class="row">
                    <label>{lang:vt.staticPage.statusId}</label>
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
        $id         = $object->staticPageId;
        $editpath   = $grid['basepath'] . "edit/" . $id;
?>
			<tr data-object-id="{$id}">
                <td class="header">{$object.title}</td>
                <td class="left">{$object.url}</td>
                <td>{$object.orderNumber}</td>
                <td><?= !empty($object->parentStaticPage) ? $object->parentStaticPage->title : "" ?></td>
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