<?php
    /** @var string[] $search */
    /** @var bool $hideSearch */
    /** @var string $sortField */
    /** @var string $sortType */

    use Eaze\Helpers\FormHelper;
    use Eaze\Site\Site;

    /** @var Photo[] $list */
    /** @var Album[] $albums */
    

    $__pageTitle = T( 'vt.screens.photo.list');

    $grid = [
        'columns'     => [
            T( 'vt.photo.albumId' ),
            T( 'vt.photo.originalName' ),
            T( 'vt.photo.orderNumber' ),
            T( 'vt.photo.photoDate' ),
            T( 'vt.photo.statusId' ),
        ],
        'colspans'    => [ 0 => 2 ],
        'sorts'       => [ 0 => 'album.title', 1 => 'originalName', 2 => 'orderNumber', 3 => 'photoDate', 4 => 'statusId', ],
        'operations'  => true,
        'allowAdd'    => true,
        'canPages'    => PhotoFactory::CanPages(),
        'basePath'    => Site::GetWebPath( 'vt://photos/' ),
        'addPath'     => Site::GetWebPath( 'vt://photos/add' ),
        'title'       => $__pageTitle,
        'description' => '',
        'pageSize'    => FormHelper::RenderToForm( $search['pageSize'] ),
        'deleteStr'   => T( 'vt.photo.deleteString' ),
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
                        <div class="col3 alignRight"><label for="albumId" class="blockLabel _shiftToRight">{lang:vt.photo.albumId}</label></div>
                        <div class="col6"><?= FormHelper::FormSelect( 'search[albumId]', $albums, 'albumId', 'title', $search['albumId'], null, 'select2', true ); ?></div>
                        <?= VtHelper::GetExtendedSearchHtml(); ?>
                    </div>
                    <div class="row _fluid _p">
                        <div class="col3 alignRight"><label for="originalName" class="blockLabel _shiftToRight">{lang:vt.photo.originalName}</label></div>
                        <div class="col6"><?= FormHelper::FormInput( 'search[originalName]', $search['originalName'], 'originalName' ); ?></div>
                    </div>
                    <div id="ExtendedSearch" class="displayNone" style="display: none;">
                        <div class="row _fluid _p">
                            <div class="col3 alignRight"><label for="orderNumber" class="blockLabel _shiftToRight">{lang:vt.photo.orderNumber}</label></div>
                            <div class="col6"><?= FormHelper::FormInput( 'search[orderNumber]', $search['orderNumber'], 'orderNumber' ); ?></div>
                        </div>
                        <div class="row _fluid _p">
                            <div class="col3 alignRight"><label for="afterText" class="blockLabel _shiftToRight">{lang:vt.photo.afterText}</label></div>
                            <div class="col6"><?= FormHelper::FormInput( 'search[afterText]', $search['afterText'], 'afterText' ); ?></div>
                        </div>
                        <div class="row _fluid _p">
                            <div class="col3 alignRight"><label for="title" class="blockLabel _shiftToRight">{lang:vt.photo.title}</label></div>
                            <div class="col6"><?= FormHelper::FormInput( 'search[title]', $search['title'], 'title' ); ?></div>
                        </div>
                        <div class="row _fluid _p">
                            <div class="col3 alignRight"><label for="statusId" class="blockLabel _shiftToRight">{lang:vt.photo.statusId}</label></div>
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
            $id       = $object->photoId;
            $editPath = $grid['basePath'] . 'edit/' . $id;
    ?>
                <tr data-object-id="{$id}">
                    <td><img src="<?= LinkUtility::GetPhotoThumb( $object, true ) ?>" width="30"></td>
                    <td>{$object.album.title}</td>
                    <td>{form:$object.originalName} <? if ( $object->afterText ) { ?><i class="fsText cFade foundicon-page" title="{form:$object.afterText}"></i><? } ?></td>
                    <td class="alignRight"><? if ( $object->orderNumber !== null ) { ?>{num:$object.orderNumber}<? } ?></td>
                    <td class="alignCenter"><?= $object->photoDate ? $object->photoDate->DefaultFormat() : '' ?></td>
                    <td><?= StatusUtility::GetStatusTemplate( $object->statusId ) ?></td>
                    <td class="tableControls"><a href="{$editPath}" title="{$langEdit}"><i class="foundicon-edit"></i> {$langEdit}</a> <a href="#" class="delete-object" title="{langDelete}"><i class="foundicon-remove"></i></a></td>
                </tr>
    <? } ?>
    {increal:tmpl://vt/elements/datagrid/footer.tmpl.php}
</main>
{increal:tmpl://vt/elements/footer.tmpl.php}
