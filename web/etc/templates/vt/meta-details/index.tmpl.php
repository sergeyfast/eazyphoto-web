<?php
    /** @var string[] $search */
    /** @var bool $hideSearch */
    /** @var string $sortField */
    /** @var string $sortType */

    use Eaze\Helpers\FormHelper;
    use Eaze\Site\Site;

    /** @var MetaDetail[] $list */
    

    $__pageTitle = T( 'vt.screens.metaDetail.list');

    $grid = [
        'columns'     => [
            T( 'vt.metaDetail.objectId' ),
            T( 'vt.metaDetail.objectClass' ),
            T( 'vt.metaDetail.url' ),
            T( 'vt.metaDetail.pageTitle' ),
            T( 'vt.metaDetail.metaKeywords' ),
            T( 'vt.metaDetail.metaDescription' ),
            T( 'vt.metaDetail.alt' ),
            T( 'vt.metaDetail.canonicalUrl' ),
            T( 'vt.metaDetail.statusId' ),
        ],
        'colspans'    => [],
        'sorts'       => [ 0 => 'objectId', 1 => 'objectClass', 2 => 'url', 3 => 'pageTitle', 4 => 'metaKeywords', 5 => 'metaDescription', 6 => 'alt', 7 => 'canonicalUrl', 8 => 'statusId', ],
        'operations'  => true,
        'allowAdd'    => true,
        'canPages'    => MetaDetailFactory::CanPages(),
        'basePath'    => Site::GetWebPath( 'vt://meta-details/' ),
        'addPath'     => Site::GetWebPath( 'vt://meta-details/add' ),
        'title'       => $__pageTitle,
        'description' => '',
        'pageSize'    => FormHelper::RenderToForm( $search['pageSize'] ),
        'deleteStr'   => T( 'vt.metaDetail.deleteString' ),
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
                        <div class="col3 alignRight"><label for="url" class="blockLabel _shiftToRight">{lang:vt.metaDetail.url}</label></div>
                        <div class="col6"><?= FormHelper::FormInput( 'search[url]', $search['url'], 'url' ); ?></div>
                    </div>
                    <div class="row _fluid _p">
                        <div class="col3 alignRight"><label for="pageTitle" class="blockLabel _shiftToRight">{lang:vt.metaDetail.pageTitle}</label></div>
                        <div class="col6"><?= FormHelper::FormInput( 'search[pageTitle]', $search['pageTitle'], 'pageTitle' ); ?></div>
                    </div>
                    <div class="row _fluid _p">
                        <div class="col3 alignRight"><label for="metaKeywords" class="blockLabel _shiftToRight">{lang:vt.metaDetail.metaKeywords}</label></div>
                        <div class="col6"><?= FormHelper::FormInput( 'search[metaKeywords]', $search['metaKeywords'], 'metaKeywords' ); ?></div>
                    </div>
                    <div class="row _fluid _p">
                        <div class="col3 alignRight"><label for="metaDescription" class="blockLabel _shiftToRight">{lang:vt.metaDetail.metaDescription}</label></div>
                        <div class="col6"><?= FormHelper::FormInput( 'search[metaDescription]', $search['metaDescription'], 'metaDescription' ); ?></div>
                    </div>
                    <div class="row _fluid _p">
                        <div class="col3 alignRight"><label for="alt" class="blockLabel _shiftToRight">{lang:vt.metaDetail.alt}</label></div>
                        <div class="col6"><?= FormHelper::FormInput( 'search[alt]', $search['alt'], 'alt' ); ?></div>
                    </div>
                    <div class="row _fluid _p">
                        <div class="col3 alignRight"><label for="objectClass" class="blockLabel _shiftToRight">{lang:vt.metaDetail.objectClass}</label></div>
                        <div class="col6"><?= FormHelper::FormInput( 'search[objectClass]', $search['objectClass'], 'objectClass' ); ?></div>
                    </div>
                    <div class="row _fluid _p">
                        <div class="col3 alignRight"><label for="objectId" class="blockLabel _shiftToRight">{lang:vt.metaDetail.objectId}</label></div>
                        <div class="col6"><?= FormHelper::FormInput( 'search[objectId]', $search['objectId'], 'objectId' ); ?></div>
                    </div>
                    <div class="row _fluid _p">
                        <div class="col3 alignRight"><label for="canonicalUrl" class="blockLabel _shiftToRight">{lang:vt.metaDetail.canonicalUrl}</label></div>
                        <div class="col6"><?= FormHelper::FormInput( 'search[canonicalUrl]', $search['canonicalUrl'], 'canonicalUrl' ); ?></div>
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
            $id       = $object->metaDetailId;
            $editPath = $grid['basePath'] . 'edit/' . $id;
    ?>
                <tr data-object-id="{$id}">
                    <td class="alignRight"><? if ( $object->objectId !== null ) { ?>{num:$object.objectId}<? } ?></td>
                    <td>{form:$object.objectClass}</td>
                    <td>{form:$object.url}</td>
                    <td>{form:$object.pageTitle}</td>
                    <td>{form:$object.metaKeywords}</td>
                    <td>{form:$object.metaDescription}</td>
                    <td>{form:$object.alt}</td>
                    <td>{form:$object.canonicalUrl}</td>
                    <td><?= StatusUtility::GetStatusTemplate( $object->statusId ) ?></td>
                    <td class="tableControls"><a href="{$editPath}" title="{$langEdit}"><i class="foundicon-edit"></i> {$langEdit}</a> <a href="#" class="delete-object" title="{langDelete}"><i class="foundicon-remove"></i></a></td>
                </tr>
    <? } ?>
    {increal:tmpl://vt/elements/datagrid/footer.tmpl.php}
</main>
{increal:tmpl://vt/elements/footer.tmpl.php}
