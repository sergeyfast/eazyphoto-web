<?php
    /** @var string[] $search */
    /** @var bool $hideSearch */
    /** @var string $sortField */
    /** @var string $sortType */

    use Eaze\Helpers\FormHelper;
    use Eaze\Site\Site;

    /** @var Navigation[] $list */
    /** @var NavigationType[] $navigationTypes */
    /** @var StaticPage[] $staticPages */
    

    $__pageTitle = T( 'vt.screens.navigation.list');

    $grid = [
        'columns'     => [
            T( 'vt.navigation.title' ),
            T( 'vt.navigation.orderNumber' ),
            T( 'vt.navigation.navigationTypeId' ),
            T( 'vt.navigation.url' ),
            T( 'vt.navigation.staticPageId' ),
            T( 'vt.navigation.statusId' ),
        ],
        'colspans'    => [],
        'sorts'       => [ 0 => 'title', 1 => 'orderNumber', 2 => 'navigationType.title', 3 => 'url', 4 => 'staticPage.title', 5 => 'statusId', ],
        'operations'  => true,
        'allowAdd'    => true,
        'canPages'    => NavigationFactory::CanPages(),
        'basePath'    => Site::GetWebPath( 'vt://navigations/' ),
        'addPath'     => Site::GetWebPath( 'vt://navigations/add' ),
        'title'       => $__pageTitle,
        'description' => '',
        'pageSize'    => FormHelper::RenderToForm( $search['pageSize'] ),
        'deleteStr'   => T( 'vt.navigation.deleteString' ),
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
                        <div class="col3 alignRight"><label for="title" class="blockLabel _shiftToRight">{lang:vt.navigation.title}</label></div>
                        <div class="col6"><?= FormHelper::FormInput( 'search[title]', $search['title'], 'title' ); ?></div>
                    </div>
                    <div class="row _fluid _p">
                        <div class="col3 alignRight"><label for="navigationTypeId" class="blockLabel _shiftToRight">{lang:vt.navigation.navigationTypeId}</label></div>
                        <div class="col6"><?= FormHelper::FormSelect( 'search[navigationTypeId]', $navigationTypes, 'navigationTypeId', 'title', $search['navigationTypeId'], null, null, true ); ?></div>
                    </div>
                    <div class="row _fluid _p">
                        <div class="col3 alignRight"><label for="statusId" class="blockLabel _shiftToRight">{lang:vt.navigation.statusId}</label></div>
                        <div class="col6"><?= FormHelper::FormSelect( 'search[statusId]', StatusUtility::$Common[$__currentLang], '', '', $search['statusId'], null, null, true ); ?></div>
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
            $id       = $object->navigationId;
            $editPath = $grid['basePath'] . 'edit/' . $id;
    ?>
                <tr data-object-id="{$id}">
                    <td><strong>{$object.title}</strong></td>
                    <td class="alignRight"><? if ( $object->orderNumber !== null ) { ?>{num:$object.orderNumber}<? } ?></td>
                    <td>{$object.navigationType.title}</td>
                    <td>{form:$object.url}</td>
                    <td><?= $object->staticPage ? $object->staticPage->title : '' ?></td>
                    <td><?= StatusUtility::GetStatusTemplate( $object->statusId ) ?></td>
                    <td class="tableControls"><a href="{$editPath}" title="{$langEdit}"><i class="foundicon-edit"></i> {$langEdit}</a> <a href="#" class="delete-object" title="{langDelete}"><i class="foundicon-remove"></i></a></td>
                </tr>
    <? } ?>
    {increal:tmpl://vt/elements/datagrid/footer.tmpl.php}
</main>
{increal:tmpl://vt/elements/footer.tmpl.php}
