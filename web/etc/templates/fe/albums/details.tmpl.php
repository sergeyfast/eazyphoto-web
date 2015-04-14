<?php
    /** @var Album $album */
    /** @var Photo[] $photos */

    use Eaze\Helpers\CssHelper;
    use Eaze\Helpers\JsHelper;

    $__pageTitle  = $album->title;
    $firstPhoto   = $photos ? reset( $photos ) : null;
    $album->Photo = $firstPhoto;
    if ( $firstPhoto && !$firstPhoto->orderNumber ) {
        $firstPhoto = null;
    }

    $__metaDescription = $album->description;
    $__ogImage         = $album->Photo ? LinkUtility::GetPhotoHd( $album->Photo, true ) : null;

    $keywords = [ ];
    foreach ( $album->Tags + $album->AllTags as $t ) {
        $keywords[] = $t->title;
    }
    $__metaKeywords = implode( ', ', $keywords );
?>
{increal:tmpl://fe/elements/header.tmpl.php}
<?
    CssHelper::PushFile( 'css://fancybox/jquery.fancybox.css' );
    JsHelper::PushFiles( [ 'js://fe/jquery.fancybox.js', 'js://fe/album.js' ] );
?>
<? if ( $firstPhoto ) { ?>
<div class="present">
    <div style="background-image: url(<?= LinkUtility::GetPhotoHd( $firstPhoto, true ) ?>)" class="_background"></div>
    <div class="_text">
        <h1>{$album.title}</h1>
        <p class="cFirm"><?= AlbumHelper::GetDate( $album ) ?>, {$album.Count()} фото</p>
        <script type="text/javascript" src="//yastatic.net/share/share.js" charset="utf-8"></script>
        <div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="small" data-yashareQuickServices="vkontakte,facebook,twitter,odnoklassniki,gplus" data-yashareTheme="counter" data-yashareImage="<?= LinkUtility::GetPhotoHd( $firstPhoto, true ) ?>"></div>
    </div>
</div>
<? } ?>
<div class="container">
    {increal:tmpl://fe/elements/breadcrumbs.tmpl.php}
    <? if ( !$firstPhoto ) { ?>
    <h1>{$album.title}</h1>
    <p class="cFirm"><?= AlbumHelper::GetDate( $album ) ?></p>
    <? } ?>

    <? $hasTitle = false; ?>
    <? if ( $album->description ) { ?><p class="fsBig">{$album.description}</p><? } ?>
    <? foreach( $photos as $photo ) { ?>
        <? if ( !$photo->orderNumber || $photo->orderNumber === 1) continue; ?>
    <div class="marginTopBase"><img src="<?= LinkUtility::GetPhotoHd( $photo, true ) ?>" alt="{$photo.title}"></div>
    <p>{$photo.afterText}</p>
        <? $hasTitle = true; ?>
    <? } ?>

    <? if ( $hasTitle ) { ?><h2>Фотоальбом</h2><? } ?>
    <? if ( $photos ) { ?>
    <ul class="objList alignCenter">
        <? foreach( $photos as $p ) { ?>
        <li><a class="fancybox" href="<?= LinkUtility::GetPhotoHd( $p, true ) ?>" rel="gallery"><img width="178" height="178" src="<?= LinkUtility::GetPhotoThumb( $p, true ) ?>" alt="{form:$p.title}"></a></li>
        <? } ?>
    </ul>
    <? } ?>
    <div class="row">
        <div class="col4">
        <? if ( $album->Tags ) { ?>
            <h3>Теги</h3>
            <p><?= TagHelper::GetTagLinks( $album->Tags )?> <? if ( $album->AllTags ) { ?><?= TagHelper::GetTagLinks( $album->AllTags )?><? } ?></p>
        <? } ?>
        </div>
        <? if ( $album->metaInfo && !empty( $album->metaInfo['size'] ) ) { ?>
        <div class="col8">
            <h3>Скачать через BitTorrent Sync</h3>
            <p><a href="#" class="copy-to-clipboard">{$album.roSecret}</a> — Оригиналы (({$album.metaInfo[count]} фото, <?= round( $album->metaInfo['size'] / 1024 / 1024  ) ?> МБ)<br>
            <? if ( $album->roSecretHd ) { ?><a href="#" class="copy-to-clipboard">{$album.roSecretHd}</a> &mdash; HD-качество ({$album.metaInfo[count]} фото, <?= round( $album->metaInfo['sizeHd'] / 1024 / 1024  ) ?> МБ)<? } ?>
            </p>
        </div>
        <? } ?>
    </div>
</div>
{increal:tmpl://fe/elements/footer.tmpl.php}