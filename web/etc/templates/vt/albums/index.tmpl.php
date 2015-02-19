<?php
    /** @var string[] $search */
    /** @var bool $hideSearch */
    /** @var string $sortField */
    /** @var string $sortType */

    use Eaze\Helpers\FormHelper;
    use Eaze\Site\Site;

    /** @var Album[] $list */
    /** @var User[] $users */
    

    $__pageTitle = T( 'vt.screens.album.list');

    $grid = [
        'columns'     => [
            T( 'vt.album.title' ),
            T( 'vt.album.alias' ),
            T( 'vt.album.isPrivate' ),
            T( 'vt.album.startDate' ),
            T( 'vt.album.roSecret' ),
            T( 'vt.album.statusId' ),
        ],
        'colspans'    => [],
        'sorts'       => [ 0 => 'title', 1 => 'alias', 2 => 'isPrivate', 3 => 'startDate', 4 => 'roSecret', 5 => 'statusId', ],
        'operations'  => true,
        'allowAdd'    => true,
        'canPages'    => AlbumFactory::CanPages(),
        'basePath'    => Site::GetWebPath( 'vt://albums/' ),
        'addPath'     => Site::GetWebPath( 'vt://albums/add' ),
        'title'       => $__pageTitle,
        'description' => '',
        'pageSize'    => FormHelper::RenderToForm( $search['pageSize'] ),
        'deleteStr'   => T( 'vt.album.deleteString' ),
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
                        <div class="col3 alignRight"><label for="title" class="blockLabel _shiftToRight">{lang:vt.album.title}</label></div>
                        <div class="col6"><?= FormHelper::FormInput( 'search[title]', $search['title'], 'title' ); ?></div>
                    </div>
                    <div class="row _fluid _p">
                        <div class="col3 alignRight"><label for="description" class="blockLabel _shiftToRight">{lang:vt.album.description}</label></div>
                        <div class="col6"><?= FormHelper::FormInput( 'search[description]', $search['description'], 'description' ); ?></div>
                    </div>
                    <div class="row _fluid _p">
                        <div class="col3 alignRight"><label for="alias" class="blockLabel _shiftToRight">{lang:vt.album.alias}</label></div>
                        <div class="col6"><?= FormHelper::FormInput( 'search[alias]', $search['alias'], 'alias' ); ?></div>
                    </div>
                    <div class="row _fluid _p">
                        <div class="col3 alignRight"><label for="roSecret" class="blockLabel _shiftToRight">{lang:vt.album.roSecret}</label></div>
                        <div class="col6"><?= FormHelper::FormInput( 'search[roSecret]', $search['roSecret'], 'roSecret' ); ?></div>
                    </div>
                    <div class="row _fluid _p">
                        <div class="col3 alignRight"><label for="statusId" class="blockLabel _shiftToRight">{lang:vt.album.statusId}</label></div>
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
            $id       = $object->albumId;
            $editPath = $grid['basePath'] . 'edit/' . $id;
    ?>
                <tr data-object-id="{$id}">
                    <td><strong>{$object.title}</strong></td>
                    <td>{form:$object.alias}</td>
                    <td><?= VtHelper::GetBoolTemplate( $object->isPrivate ) ?></td>
                    <td class="alignCenter"><?= $object->startDate ? $object->startDate->DefaultDateFormat() : '' ?></td>
                    <td>{form:$object.roSecret}</td>
                    <td><?= StatusUtility::GetStatusTemplate( $object->statusId ) ?></td>
                    <td class="tableControls"><a href="{$editPath}" title="{$langEdit}"><i class="foundicon-edit"></i> {$langEdit}</a> <a href="#" class="delete-object" title="{langDelete}"><i class="foundicon-remove"></i></a></td>
                </tr>
    <? } ?>
    {increal:tmpl://vt/elements/datagrid/footer.tmpl.php}
</main>
{increal:tmpl://vt/elements/footer.tmpl.php}
