<?php
    /** @var MetaDetail $object */
    /** @var int $objectId */

    use Eaze\Helpers\FormHelper;
    use Eaze\Helpers\JsHelper;
    use Eaze\Helpers\SecureTokenHelper;
    use Eaze\Model\BaseSaveAction;
    use Eaze\Site\Site;

    JsHelper::PushLine(
        sprintf( "var objectBasePath = '%s', objectDeleteStr = '%s';",
            Site::GetWebPath( 'vt://meta-details/' ),
            T( 'vt.metaDetail.deleteString' )
        )
    );

    $__pageTitle   = T( 'vt.screens.metaDetail.editTitle' );
    $__breadcrumbs = [
        [ 'link' => Site::GetWebPath( 'vt://meta-details/' ), 'title' => T( 'vt.screens.metaDetail.list' ) ],
        [ 'link' => Site::GetWebPath( 'vt://meta-details/edit/' . $objectId ), 'title' => T( 'vt.common.crumbEdit' ) ],
    ];
?>
{increal:tmpl://vt/elements/header.tmpl.php}
<main role="main">
    {increal:tmpl://vt/elements/menu/breadcrumbs.tmpl.php}
    <div class="container"><a href="{web:vt://meta-details/}" class="linkBlock cLink fsBigX floatRight">← <span class="link">{lang:vt.common.back}</span></a>
        <h1>{$__pageTitle}</h1>
        <form method="post" action="{web:vt://meta-details/}edit/{$objectId}" enctype="multipart/form-data" id="data-form" data-object-id="{$objectId}" >
            <?= FormHelper::FormHidden( 'action', BaseSaveAction::UpdateAction ); ?>
            <?= FormHelper::FormHidden( 'redirect', '', 'redirect' ); ?>
            <?= SecureTokenHelper::FormHidden(); ?>
            {increal:tmpl://vt/meta-details/data.tmpl.php}
            <div class="tabsFakeCont">
                <div class="row _fluid">
                    <div class="col2"><p><a href="{web:vt://meta-details/}" class="linkInlineBlock cLink fsBigX linkInlineBlock">← <span class="link">{lang:vt.common.back}</span></a></p></div>
                    <div class="col6"><button type="submit" class="button _big marginRightBase"><i class="fsText foundicon-checkmark"></i> {lang:vt.common.saveChanges}</button>
                        <button type="submit" class="button _big _light edit-preview"><i class="fsText foundicon-checkmark cFade"></i> {lang:vt.common.editPreview}</button></div>
                    <div class="col4 alignRight"><a href="#" class="button _big _del delete-object-return"><i class="fsText foundicon-trash"></i> {lang:vt.common.delete}</a></p></div>
                </div>
            </div>
        </form>
    </div>
</main>
{increal:tmpl://vt/elements/footer.tmpl.php}
