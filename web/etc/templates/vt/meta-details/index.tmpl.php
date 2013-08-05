<?php
    /** @var MetaDetail[] $list */

    $__pageTitle = LocaleLoader::Translate( "vt.screens.metaDetail.list");

    $grid = array(
        "columns" => array(
           LocaleLoader::Translate( "vt.metaDetail.url" )
            , LocaleLoader::Translate( "vt.metaDetail.pageTitle" )
            , LocaleLoader::Translate( "vt.metaDetail.metaKeywords" )
            , LocaleLoader::Translate( "vt.metaDetail.metaDescription" )
            , LocaleLoader::Translate( "vt.metaDetail.alt" )
            , LocaleLoader::Translate( "vt.metaDetail.statusId" )
        )
        , "colspans"	=> array()
        , "sorts"		=> array(0 => "url", 1 => "pageTitle", 2 => "metaKeywords", 3 => "metaDescription", 4 => "alt", 5 => "isInheritable", 6 => "statusId")
        , "operations"	=> true
        , "allowAdd"	=> true
        , "canPages"	=> MetaDetailFactory::CanPages()
        , "basepath"	=> Site::GetWebPath( "vt://meta-details/" )
        , "addpath"		=> Site::GetWebPath( "vt://meta-details/add" )
        , "title"		=> $__pageTitle
		, "description"	=> ''
        , "pageSize"	=> HtmlHelper::RenderToForm( $search["pageSize"] )
        , "deleteStr"	=> LocaleLoader::Translate( "vt.metaDetail.deleteString")
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
                    <label>{lang:vt.metaDetail.url}</label>
                    <?= FormHelper::FormInput( "search[url]", $search['url'], 'url', null, array( 'size' => 80 ) ); ?>
                </div>
                <div class="row">
                    <label>{lang:vt.metaDetail.pageTitle}</label>
                    <?= FormHelper::FormInput( "search[pageTitle]", $search['pageTitle'], 'pageTitle', null, array( 'size' => 80 ) ); ?>
                </div>
                <div class="row">
                    <label>{lang:vt.metaDetail.metaKeywords}</label>
                    <?= FormHelper::FormInput( "search[metaKeywords]", $search['metaKeywords'], 'metaKeywords', null, array( 'size' => 80 ) ); ?>
                </div>
                <div class="row">
                    <label>{lang:vt.metaDetail.metaDescription}</label>
                    <?= FormHelper::FormInput( "search[metaDescription]", $search['metaDescription'], 'metaDescription', null, array( 'size' => 80 ) ); ?>
                </div>
                <div class="row">
                    <label>{lang:vt.metaDetail.alt}</label>
                    <?= FormHelper::FormInput( "search[alt]", $search['alt'], 'alt', null, array( 'size' => 80 ) ); ?>
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
        $id         = $object->metaDetailId;
        $editpath   = $grid['basepath'] . "edit/" . $id;
?>
			<tr data-object-id="{$id}">
                <td class="header">{$object.url}</td>
                <td>{$object.pageTitle}</td>
                <td>{$object.metaKeywords}</td>
                <td>{$object.metaDescription}</td>
                <td>{$object.alt}</td>
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