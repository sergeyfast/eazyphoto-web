<?php
    /** @var MetaDetail $object */

    use Eaze\Helpers\FormHelper;
    use Eaze\Helpers\SecureTokenHelper;
    use Eaze\Model\BaseSaveAction;
    use Eaze\Site\Site;

    $__pageTitle   = T( 'vt.screens.metaDetail.addTitle' );
    $__breadcrumbs = [
        [ 'link' => Site::GetWebPath( 'vt://meta-details/' ), 'title' => T( 'vt.screens.metaDetail.list' ) ],
        [ 'link' => Site::GetWebPath( 'vt://meta-details/add' ), 'title' => T( 'vt.common.crumbAdd' ) ],
    ];
?>
{increal:tmpl://vt/elements/header.tmpl.php}
<main role="main">
    {increal:tmpl://vt/elements/menu/breadcrumbs.tmpl.php}
    <div class="container"><a href="{web:vt://meta-details/}" class="linkBlock cLink fsBigX floatRight">← <span class="link">{lang:vt.common.back}</span></a>
        <h1>{$__pageTitle}</h1>
        <form method="post" action="{web:vt://meta-details/}add" enctype="multipart/form-data" id="data-form">
            <?= FormHelper::FormHidden( 'action', BaseSaveAction::AddAction ); ?>
            <?= SecureTokenHelper::FormHidden(); ?>
            {increal:tmpl://vt/meta-details/data.tmpl.php}
            <div class="tabsFakeCont">
                <div class="row _fluid">
                    <div class="col2"><p><a href="{web:vt://meta-details/}" class="linkInlineBlock cLink fsBigX linkInlineBlock">← <span class="link">{lang:vt.common.back}</span></a></p></div>
                    <div class="col6"><button type="submit" class="button _big marginRightBase"><i class="fsText foundicon-checkmark"></i> {lang:vt.common.saveChanges}</button></div>
                </div>
            </div>
        </form>
    </div>
</main>
{increal:tmpl://vt/elements/footer.tmpl.php}
