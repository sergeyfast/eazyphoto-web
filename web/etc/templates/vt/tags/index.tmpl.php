<?php
    /** @var string[] $search */
    /** @var bool $hideSearch */
    /** @var string $sortField */
    /** @var string $sortType */

    use Eaze\Helpers\FormHelper;
    use Eaze\Site\Site;

    /** @var Tag[] $list */
    /** @var Tag[] $parentTags */
    

    $__pageTitle = T( 'vt.screens.tag.list');

    $grid = [
        'columns'     => [
            T( 'vt.tag.title' ),
            T( 'vt.tag.alias' ),
            T( 'vt.tag.orderNumber' ),
            T( 'vt.tag.parentTagId' ),
            T( 'vt.tag.statusId' ),
        ],
        'colspans'    => [],
        'sorts'       => [ 0 => 'title', 1 => 'alias', 2 => 'orderNumber', 3 => 'parentTag.title', 4 => 'statusId', ],
        'operations'  => true,
        'allowAdd'    => true,
        'canPages'    => TagFactory::CanPages(),
        'basePath'    => Site::GetWebPath( 'vt://tags/' ),
        'addPath'     => Site::GetWebPath( 'vt://tags/add' ),
        'title'       => $__pageTitle,
        'description' => '',
        'pageSize'    => FormHelper::RenderToForm( $search['pageSize'] ),
        'deleteStr'   => T( 'vt.tag.deleteString' ),
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
                        <div class="col3 alignRight"><label for="title" class="blockLabel _shiftToRight">{lang:vt.tag.title}</label></div>
                        <div class="col6"><?= FormHelper::FormInput( 'search[title]', $search['title'], 'title' ); ?></div>
                        <?= VtHelper::GetExtendedSearchHtml() ?>
                    </div>
                    <div class="row _fluid _p">
                        <div class="col3 alignRight"><label for="parentTagId" class="blockLabel _shiftToRight">{lang:vt.tag.parentTagId}</label></div>
                        <div class="col6"><?= FormHelper::FormSelect( 'search[parentTagId]', $parentTags, 'tagId', 'title', $search['parentTagId'], null, 'select2', true ); ?></div>
                    </div>
                    <div id="ExtendedSearch" class="displayNone" style="display: none;">
                        <div class="row _fluid _p">
                            <div class="col3 alignRight"><label for="alias" class="blockLabel _shiftToRight">{lang:vt.tag.alias}</label></div>
                            <div class="col6"><?= FormHelper::FormInput( 'search[alias]', $search['alias'], 'alias' ); ?></div>
                        </div>
                        <div class="row _fluid _p">
                            <div class="col3 alignRight"><label for="orderNumber" class="blockLabel _shiftToRight">{lang:vt.tag.orderNumber}</label></div>
                            <div class="col6"><?= FormHelper::FormInput( 'search[orderNumber]', $search['orderNumber'], 'orderNumber' ); ?></div>
                        </div>
                        <div class="row _fluid _p">
                            <div class="col3 alignRight"><label for="statusId" class="blockLabel _shiftToRight">{lang:vt.tag.statusId}</label></div>
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
            $id       = $object->tagId;
            $editPath = $grid['basePath'] . 'edit/' . $id;
    ?>
                <tr data-object-id="{$id}">
                    <td><strong>{$object.title}</strong></td>
                    <td>{form:$object.alias}</td>
                    <td class="alignRight"><? if ( $object->orderNumber !== null ) { ?>{num:$object.orderNumber}<? } ?></td>
                    <td><?= $object->parentTag ? $object->parentTag->title : '' ?></td>
                    <td><?= StatusUtility::GetStatusTemplate( $object->statusId ) ?></td>
                    <td class="tableControls"><a href="{$editPath}" title="{$langEdit}"><i class="foundicon-edit"></i> {$langEdit}</a> <a href="#" class="delete-object" title="{langDelete}"><i class="foundicon-remove"></i></a></td>
                </tr>
    <? } ?>
    {increal:tmpl://vt/elements/datagrid/footer.tmpl.php}
</main>
{increal:tmpl://vt/elements/footer.tmpl.php}
