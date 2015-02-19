<?php
    /** @var string[] $search */
    /** @var bool $hideSearch */
    /** @var string $sortField */
    /** @var string $sortType */

    use Eaze\Helpers\FormHelper;
    use Eaze\Site\Site;

    /** @var StaticPage[] $list */
    /** @var StaticPage[] $staticPages */
    

    $__pageTitle = T( 'vt.screens.staticPage.list');

    $grid = [
        'columns'     => [
            T( 'vt.staticPage.title' ),
            T( 'vt.staticPage.url' ),
            T( 'vt.staticPage.orderNumber' ),
            T( 'vt.staticPage.parentStaticPageId' ),
            T( 'vt.staticPage.statusId' ),
        ],
        'colspans'    => [],
        'sorts'       => [ 0 => 'title', 1 => 'url', 2 => 'orderNumber', 3 => 'parentStaticPage.title', 4 => 'statusId', ],
        'operations'  => true,
        'allowAdd'    => true,
        'canPages'    => StaticPageFactory::CanPages(),
        'basePath'    => Site::GetWebPath( 'vt://static-pages/' ),
        'addPath'     => Site::GetWebPath( 'vt://static-pages/add' ),
        'title'       => $__pageTitle,
        'description' => '',
        'pageSize'    => FormHelper::RenderToForm( $search['pageSize'] ),
        'deleteStr'   => T( 'vt.staticPage.deleteString' ),
    ];

    $__breadcrumbs = [ [ 'link' => $grid['basePath'], 'title' => $__pageTitle ] ];
?>
{increal:tmpl://vt/elements/header.tmpl.php}
<main role="main">
    {increal:tmpl://vt/elements/menu/breadcrumbs.tmpl.php}
    <div class="container"><? if( $grid['allowAdd'] ) { ?><a href="{$grid[addPath]}" class="button _big floatRight marginAntiTopHalfBase"><i class="foundicon-add-doc"></i> {lang:vt.common.add}</a><? }?>
        <h1>{$__pageTitle}</h1>
        <form id="searchForm" method="post" action="{$grid[basePath]}">
            <?= FormHelper::FormHidden( 'searchForm', 1 ); ?>
            <?= FormHelper::FormHidden( 'page', '', 'pageId' ); ?>
            <?= FormHelper::FormHidden( 'search[pageSize]', $grid['pageSize'], 'pageSize' ); ?>
            <?= FormHelper::FormHidden( 'sortField', $sortField, 'sortField' ); ?>
            <?= FormHelper::FormHidden( 'sortType', $sortType, 'sortType' ); ?>

            <div class="plate cont">
                <div class="form fsMedium">
                    <div class="row _fluid _p">
                        <div class="col3 alignRight"><label for="title" class="blockLabel _shiftToRight">{lang:vt.staticPage.title}</label></div>
                        <div class="col6"><?= FormHelper::FormInput( 'search[title]', $search['title'], 'title' ); ?></div>
                        <?= VtHelper::GetExtendedSearchHtml() ?>
                    </div>
                    <div id="ExtendedSearch" class="displayNone" style="display: none;">
                        <div class="row _fluid _p">
                            <div class="col3 alignRight"><label for="url" class="blockLabel _shiftToRight">{lang:vt.staticPage.url}</label></div>
                            <div class="col6"><?= FormHelper::FormInput( 'search[url]', $search['url'], 'url' ); ?></div>
                        </div>
                        <div class="row _fluid _p">
                            <div class="col3 alignRight"><label for="content" class="blockLabel _shiftToRight">{lang:vt.staticPage.content}</label></div>
                            <div class="col6"><?= FormHelper::FormInput( 'search[content]', $search['content'], 'content' ); ?></div>
                        </div>
                        <div class="row _fluid _p">
                            <div class="col3 alignRight"><label for="parentStaticPageId" class="blockLabel _shiftToRight">{lang:vt.staticPage.parentStaticPageId}</label></div>
                            <div class="col6"><?= StaticPageHelper::FormSelect( 'search[parentStaticPageId]', $staticPages, $search['parentStaticPageId'] ); ?></div>
                        </div>
                        <div class="row _fluid _p">
                            <div class="col3 alignRight"><label for="statusId" class="blockLabel _shiftToRight">{lang:vt.staticPage.statusId}</label></div>
                            <div class="col6"><?= FormHelper::FormSelect( 'search[statusId]', StatusUtility::$Common[$__currentLang], '', '', $search['statusId'], null, null, true ); ?></div>
                        </div>
                    </div>
                    <div class="row _fluid _p"><div class="col6 offset3"><button type="submit">{lang:vt.common.find}</button></div></div>
                </div>
            </div>
        </form>
    </div>
    {increal:tmpl://vt/elements/datagrid/paginator.tmpl.php}
    {increal:tmpl://vt/elements/datagrid/header.tmpl.php}
    <?php
        $langEdit   = T( 'vt.common.edit' );
        $langDelete = T( 'vt.common.delete' );

        foreach ( $list as $object )  {
            $id       = $object->staticPageId;
            $editPath = $grid['basePath'] . 'edit/' . $id;
    ?>
                <tr data-object-id="{$id}">
                    <td><strong>{$object.title}</strong></td>
                    <td>{form:$object.url}</td>
                    <td class="alignRight"><? if ( $object->orderNumber !== null ) { ?>{num:$object.orderNumber}<? } ?></td>
                    <td><?= $object->parentStaticPage ? $object->parentStaticPage->title : '' ?></td>
                    <td><?= StatusUtility::GetStatusTemplate( $object->statusId ) ?></td>
                    <td class="tableControls"><a href="{$editPath}" title="{$langEdit}"><i class="foundicon-edit"></i> {$langEdit}</a> <a href="#" class="delete-object" title="{langDelete}"><i class="foundicon-remove"></i></a></td>
                </tr>
    <? } ?>
    {increal:tmpl://vt/elements/datagrid/footer.tmpl.php}
</main>
{increal:tmpl://vt/elements/footer.tmpl.php}
