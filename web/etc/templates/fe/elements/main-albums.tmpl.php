<? /** @var Album[] $mainAlbums  */ ?>
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