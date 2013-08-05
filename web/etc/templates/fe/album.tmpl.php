<?php
    /** @var Album $album */
    /** @var Photo[] $photos */

    $__breadcrumbs = array(
        array( 'title' => $album->startDate->format('Y'), 'path' => LinkUtility::GetAlbumsUrl( $album->startDate->format('Y') ) )
        , array( 'title' => $album->title, 'path' => LinkUtility::GetAlbumUrl( $album ) )
    );

    $__pageTitle = $album->title;
    $firstPhoto  = $photos ? reset( $photos ) : null;
?>
{increal:tmpl://fe/elements/header.tmpl.php}
{increal:tmpl://fe/elements/breadcrumbs.tmpl.php}
<?
    CssHelper::PushFile( 'js://fe/fancybox/jquery.fancybox.css' );
    JsHelper::PushFile( 'js://fe/fancybox/jquery.fancybox.pack.js' );
    JsHelper::PushFile( 'js://fe/album.js' );
?>
<div class="row">
    <h2>{$album.title} <small><?= AlbumHelper::GetDate( $album ) ?></small></h2>
    <? if ( $album->description ) { ?>
    <h5 class="subheader">{$album.description}</h5>
    <? } ?>
    <? if ( $firstPhoto && $firstPhoto->orderNumber ) { ?>
    <div class="row">
        <div class="twelve columns">
            <? foreach( $photos as $photo ) { ?>
            <? if ( !$photo->orderNumber ) break; ?>
            <p><img src="<?= LinkUtility::GetPhotoHd( $photo, true ) ?>">{$photo.afterText}</p>
            <? } ?>
        </div>
    </div>
    <? } ?>
    <div class="large-12 columns">
        <ul class="small-block-grid-3 large-block-grid-5">
            <? foreach( $photos as $p ) { ?>
            <li><a class="fancybox" href="<?= LinkUtility::GetPhotoHd( $p, true ) ?>" rel="gallery"><img src="<?= LinkUtility::GetPhotoThumb( $p, true ) ?>" alt="{form:$p.title}"></a></li>
            <? } ?>
        </ul>
    </div>
</div>

<? if ( $album->metaInfo && !empty( $album->metaInfo['size'] ) ) { ?>
<br>
<div class="row">
    <div class="large-12 small-12">
        <div class="panel">
            <h4>Скачать через BitTorrent Sync</h4>
            <p>{$album.roSecret} &mdash; Оригиналы ({$album.metaInfo[count]} фото, <?= round( $album->metaInfo['size'] / 1024 / 1024  ) ?> МБ)
                <? if ( $album->roSecretHd ) { ?>
                <br/>{$album.roSecretHd} &mdash; HD-качество ({$album.metaInfo[count]} фото, <?= round( $album->metaInfo['sizeHd'] / 1024 / 1024  ) ?> МБ)<? } ?></p>

        </div>
    </div>
</div>
<? } ?>

{increal:tmpl://fe/elements/footer.tmpl.php}