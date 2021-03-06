<?php
    /** @var AlbumByTag[] $albumsByTag  */
    use Eaze\Helpers\ArrayHelper;
    use Eaze\Helpers\TextHelper;

    /** @var Album[] $mainAlbums  */
    /** @var Tag[] $tags  */
?>
{increal:tmpl://fe/elements/header.tmpl.php}
{increal:tmpl://fe/elements/main-albums.tmpl.php}
<? if ( $tags && $albumsByTag) { ?>
    <? $firstTag = reset( $tags ); ?>
<div class="container">
    <div class="tabs">
        <div class="tabs_head">
            <ul class="metaList _nosep fsBig alignCenter tabs">
                <? foreach( $tags as $t ) { ?>
                <li<?= $firstTag->tagId === $t->tagId ? ' class="_active"' : '' ?>><a href="<?= LinkUtility::GetTagUrl( $t, true )?>">{$t.title}</a></li>
                <? } ?>
            </ul>
        </div>
        <? foreach( $tags as $t ) { ?>
        <div class="tabs_cont<?= $firstTag->tagId === $t->tagId ? ' _active"' : '' ?>">
            <? $at = ArrayHelper::GetValue( $albumsByTag, $t->tagId ); ?>
            <? if ( $at ) { ?>
            <ul class="objList alignCenter">
            <? foreach( $at->Albums as $a ) { ?>
                <? if ( !$a->Photo ) continue; ?>
                <li><a href="<?= LinkUtility::GetAlbumUrl( $a, true )?>"><img width="178" height="178" src="<?= LinkUtility::GetPhotoThumb( $a->Photo, true ) ?>" alt=""><span class="_hoverOverlay"><span class="_text"><strong>{$a.title}</strong> {$a.Count()} фото<?= $a->description ? ', рассказ' : '' ?></span></span></a></li>
            <? } ?>
            <li class="_special"><a href="<?= LinkUtility::GetTagUrl( $at->Tag, true)?>"><span class="_hoverOverlay"><span class="_text"><strong>{$at.Tag.title}</strong> {$at.Count} <?= T( 'fe.albums.a'. TextHelper::GetDeclension( $at->Count ) ) ?></span></span></a></li>
            <? } ?>
        </div>
        <? } ?>
    </div>
</div>
<? } ?>
{increal:tmpl://fe/elements/footer.tmpl.php}