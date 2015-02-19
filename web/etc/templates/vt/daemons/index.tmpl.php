<?php
    /** @var string[] $search */
    /** @var bool $hideSearch */
    /** @var string $sortField */
    /** @var string $sortType */

    use Eaze\Helpers\FormHelper;
    use Eaze\Site\Site;

    /** @var DaemonLock[] $list */
    

    $__pageTitle = T( 'vt.screens.daemonLock.list');

    $grid = [
        'columns'     => [
            T( 'vt.daemonLock.title' ),
            T( 'vt.daemonLock.packageName' ),
            T( 'vt.daemonLock.methodName' ),
            T( 'vt.daemonLock.runAt' ),
            T( 'vt.daemonLock.maxExecutionTime' ),
            T( 'vt.daemonLock.isActive' ),
        ],
        'colspans'    => [],
        'sorts'       => [ 0 => 'title', 1 => 'packageName', 2 => 'methodName', 3 => 'runAt', 4 => 'maxExecutionTime', 5 => 'isActive', ],
        'operations'  => false,
        'allowAdd'    => false,
        'canPages'    => DaemonLockFactory::CanPages(),
        'basePath'    => Site::GetWebPath( 'vt://daemons/' ),
        'addPath'     => Site::GetWebPath( 'vt://daemons/add' ),
        'title'       => $__pageTitle,
        'description' => '',
        'pageSize'    => FormHelper::RenderToForm( $search['pageSize'] ),
        'deleteStr'   => T( 'vt.daemonLock.deleteString' ),
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
                        <div class="col3 alignRight"><label for="title" class="blockLabel _shiftToRight">{lang:vt.daemonLock.title}</label></div>
                        <div class="col6"><?= FormHelper::FormInput( 'search[title]', $search['title'], 'title' ); ?></div>
                    </div>
                    <div class="row _fluid _p">
                        <div class="col3 alignRight"><label for="packageName" class="blockLabel _shiftToRight">{lang:vt.daemonLock.packageName}</label></div>
                        <div class="col6"><?= FormHelper::FormInput( 'search[packageName]', $search['packageName'], 'packageName' ); ?></div>
                    </div>
                    <div class="row _fluid _p">
                        <div class="col3 alignRight"><label for="methodName" class="blockLabel _shiftToRight">{lang:vt.daemonLock.methodName}</label></div>
                        <div class="col6"><?= FormHelper::FormInput( 'search[methodName]', $search['methodName'], 'methodName' ); ?></div>
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
            $id       = $object->daemonLockId;
            $editPath = $grid['basePath'] . 'edit/' . $id;
    ?>
                <tr data-object-id="{$id}">
                    <td><strong>{$object.title}</strong></td>
                    <td>{form:$object.packageName}</td>
                    <td>{form:$object.methodName}</td>
                    <td class="alignCenter"><?= $object->runAt ? $object->runAt->DefaultFormat() : '' ?></td>
                    <td class="alignCenter"><?= $object->maxExecutionTime ? $object->maxExecutionTime->DefaultFormat() : '' ?></td>
                    <td><?= VtHelper::GetBoolTemplate( $object->isActive ) ?></td>
                    </tr>
    <? } ?>
    {increal:tmpl://vt/elements/datagrid/footer.tmpl.php}
</main>
{increal:tmpl://vt/elements/footer.tmpl.php}
