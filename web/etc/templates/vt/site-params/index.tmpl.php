<?php
    /** @var string[] $search */
    /** @var bool $hideSearch */
    /** @var string $sortField */
    /** @var string $sortType */

    use Eaze\Helpers\FormHelper;
    use Eaze\Site\Site;

    /** @var SiteParam[] $list */
    

    $__pageTitle = T( 'vt.screens.siteParam.list');

    $grid = [
        'columns'     => [
            T( 'vt.siteParam.alias' ),
            T( 'vt.siteParam.value' ),
            T( 'vt.siteParam.description' ),
            T( 'vt.siteParam.statusId' ),
        ],
        'colspans'    => [],
        'sorts'       => [ 0 => 'alias', 1 => 'value', 2 => 'description', 3 => 'statusId', ],
        'operations'  => true,
        'allowAdd'    => true,
        'canPages'    => SiteParamFactory::CanPages(),
        'basePath'    => Site::GetWebPath( 'vt://site-params/' ),
        'addPath'     => Site::GetWebPath( 'vt://site-params/add' ),
        'title'       => $__pageTitle,
        'description' => '',
        'pageSize'    => FormHelper::RenderToForm( $search['pageSize'] ),
        'deleteStr'   => T( 'vt.siteParam.deleteString' ),
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
                        <div class="col3 alignRight"><label for="alias" class="blockLabel _shiftToRight">{lang:vt.siteParam.alias}</label></div>
                        <div class="col6"><?= FormHelper::FormInput( 'search[alias]', $search['alias'], 'alias' ); ?></div>
                    </div>
                    <div class="row _fluid _p">
                        <div class="col3 alignRight"><label for="value" class="blockLabel _shiftToRight">{lang:vt.siteParam.value}</label></div>
                        <div class="col6"><?= FormHelper::FormInput( 'search[value]', $search['value'], 'value' ); ?></div>
                    </div>
                    <div class="row _fluid _p">
                        <div class="col3 alignRight"><label for="statusId" class="blockLabel _shiftToRight">{lang:vt.siteParam.statusId}</label></div>
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
            $id       = $object->siteParamId;
            $editPath = $grid['basePath'] . 'edit/' . $id;
    ?>
                <tr data-object-id="{$id}">
                    <td><strong>{form:$object.alias}</strong></td>
                    <td>{form:$object.value}</td>
                    <td>{form:$object.description}</td>
                    <td><?= StatusUtility::GetStatusTemplate( $object->statusId ) ?></td>
                    <td class="tableControls"><a href="{$editPath}" title="{$langEdit}"><i class="foundicon-edit"></i> {$langEdit}</a> <a href="#" class="delete-object" title="{langDelete}"><i class="foundicon-remove"></i></a></td>
                </tr>
    <? } ?>
    {increal:tmpl://vt/elements/datagrid/footer.tmpl.php}
</main>
{increal:tmpl://vt/elements/footer.tmpl.php}
