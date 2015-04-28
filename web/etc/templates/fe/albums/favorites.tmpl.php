<?php
    /** @var Tag[] $tags */
    /** @var Tag[] $photos */

    use Eaze\Helpers\CssHelper;
    use Eaze\Helpers\JsHelper;

?>
{increal:tmpl://fe/elements/header.tmpl.php}
{increal:tmpl://fe/elements/main-albums.tmpl.php}
<?
    CssHelper::PushFile( 'css://fancybox/jquery.fancybox.css' );
    JsHelper::PushFiles( [ 'js://fe/jquery.fancybox.js', 'js://fe/album.js' ] );
?>
<div class="container">
    {increal:tmpl://fe/elements/breadcrumbs.tmpl.php}
    <h2>{lang:fe.favorites.title}</h2>
<? if ( $photos ) { ?>
    <ul class="objList alignCenter">
        <? foreach( $photos as $p ) { ?>
            <li><a class="fancybox" title="{form:$p.album.title}"" href="<?= LinkUtility::GetPhotoHd( $p, true ) ?>" rel="gallery"><img width="178" height="178" src="<?= LinkUtility::GetPhotoThumb( $p, true ) ?>" alt="{form:$p.album.title}"></a></li>
        <? } ?>
    </ul>
<? } ?>
</div>
{increal:tmpl://fe/elements/footer.tmpl.php}