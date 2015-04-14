<?php
    /** @var Album[] $albums  */
    /** @var Album[] $mainAlbums  */
    /** @var Tag[] $tags  */
    /** @var Tag $tag */
?>
{increal:tmpl://fe/elements/header.tmpl.php}
<? if ( $mainAlbums ) { ?>
<div class="present">
    <?
        shuffle( $mainAlbums ); // data for future slider
        foreach( $mainAlbums as $a ) {
            if ( !$a->Photo ) continue;
    ?>
    <div style="background-image: url(<?= LinkUtility::GetPhotoHd( $a->Photo, true ) ?>)" class="_background"></div>
    <div class="_text">
        <h1><a href="<?= LinkUtility::GetAlbumUrl( $a ) ?>">{$a.title}</a></h1>
        <p class="cFirm">{$a.Count()} фото<?= $a->description ? ', рассказ' : '' ?></p>
        <? if ( $a->Tags ) { ?><p><?= TagHelper::GetTagLinks( $a->Tags )?> <? if ( $a->AllTags ) { ?><?= TagHelper::GetTagLinks( $a->AllTags )?><? } ?></p><? } ?>
    </div>
            <? break; ?>
    <? } ?>
</div>
<? } ?>
<div class="container">
<? if ( $tags ) { ?>
    <ul class="metaList _nosep fsBig alignCenter cont">
        <? foreach( $tags as $t ) { ?>
        <li<?= $tag->tagId === $t->tagId ? ' class="_active"' : '' ?>><a href="<?= LinkUtility::GetTagUrl( $t, true )?>">{$t.title}</a></li>
        <? } ?>
    </ul>
<? } ?>
<? if ( $albums ) { ?>
    <ul class="objList alignCenter">
        <? foreach( $albums as $a ) { ?>
            <? if ( !$a->Photo ) continue; ?>
        <li><a href="<?= LinkUtility::GetAlbumUrl( $a, true )?>"><img src="<?= LinkUtility::GetPhotoThumb( $a->Photo, true ) ?>" alt=""><span class="_hoverOverlay"><span class="_text"><strong>{$a.title}</strong> {$a.Count()} фото<?= $a->description ? ', рассказ' : '' ?></span></span></a></li>
        <? } ?>
    </ul>
<? } ?>
</div>
{increal:tmpl://fe/elements/footer.tmpl.php}