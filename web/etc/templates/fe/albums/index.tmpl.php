<?php
    use Eaze\Helpers\ArrayHelper;
    use Eaze\Helpers\JsHelper;

    /** @var AlbumSearch $as */
    /** @var Album[] $albums */
    /** @var Photo[] $photos */
?>
{increal:tmpl://fe/elements/header.tmpl.php}
<? JsHelper::PushFile( 'js://fe/albums.js' ); ?>
<div class="container">
    {increal:tmpl://fe/elements/breadcrumbs.tmpl.php}
    <div class="row">
        <div class="col2">
            <h3 class="fsBigX">Год съёмки</h3>
            <p><?= \Eaze\Helpers\FormHelper::FormSelect( 'year', $as->Years, null, null, $as->Year, 'year', null, true, null, [ 'style' => 'max-width: 4.8em', 'data-url' => $as->GetUrl( true ) ]  ); ?></p>
            <h3 class="fsBigX">Рассказ</h3>
            <p><label class="noWrap marginRightBase"><?= \Eaze\Helpers\FormHelper::FormCheckBox( 'story', 1, 'story', null, $as->IsStory, [ 'data-url' => $as->GetUrl( false, true ) ] ); ?> Пост</label></p>
            <h3 class="fsBigX">Категории</h3>
            <ul class="flatList">
                <? foreach( $as->TagMap as $t ) { ?>
                    <? if ( !$t->parentTagId ) { ?>
                <li<?= $as->Tag && ( $as->Tag->tagId === $t->tagId || $as->Tag->parentTagId === $t->tagId  ) ? ' class="_active"' : '' ?>><a href="<?= $as->GetUrlV( null, null, $t->alias  )?>">{$t.title}</a></li>
                    <? } ?>
                <? } ?>
            </ul>
            <h3 class="fsBigX">Теги</h3>
            <p><? foreach( $as->TagMap as $t ) { ?>
                <? if ( ( $t->parentTagId && !$as->Tag)  || ( $t->parentTagId && $as->Tag && ( $as->Tag->tagId === $t->parentTagId || $as->Tag->parentTagId === $t->parentTagId ) ) ) { ?><a class="tag<?= $as->Tag && $as->Tag->tagId === $t->tagId ? ' _active' : '' ?>" href="<?= $as->GetUrlV( null, null, $t->alias )?>">{$t.title}</a> <? } ?>
            <? }?></p>
        </div>
        <div class="col10">
            <? if ( $as->Sort === 'event' ) { ?>
            <div class="sortNav">Сортировать по дате:<span><b>события</b></span> <span><a href="{$as.GetSortUrl()}created">добавления</a></span></div>
            <? } else { ?>
            <div class="sortNav">Сортировать по дате:<span><a href="{$as.GetSortUrl()}event">события</a></span> <span><b>добавления</b></span></div>
            <? } ?>
            <h1>Альбомы</h1>
<?php
    foreach ( $albums as $a ) {
        if ( !$a->metaInfo || empty( $a->metaInfo['count'] ) || empty( $a->metaInfo['photoIds'] ) ) {
            continue;
        }

        $url = LinkUtility::GetAlbumUrl( $a, true );
        $i   = 0;

        if ( !$a->orderNumber ) {
            ?>
            <h3><a href="{$url}" class="marginRightHalfBase">{$a.title}</a> <span class="subHeader"><?= AlbumHelper::GetDate( $a ) ?>, <?= $a->metaInfo['count'] ?> фото<?= $a->isPrivate ? ', приватный ' : '' ?></span></h3>
            <ul class="objList">
            <?
            foreach ( $a->metaInfo['photoIds'] as $photoId ) {
                $photo = ArrayHelper::GetValue( $photos, $photoId );
                if ( !$photo ) {
                    continue;
                }

                $i ++;
                $classes = [];
                if ( $i >= 4 ) {
                    $classes[] = 'hideOnMin';
                }

                if ( $i >= 5 ) {
                    $classes[] = 'hideOnMed';
                }

                if ( $i > 5 ) {
                    break;
                }
                ?>
                <li<?= $classes ? ' class="' . implode( ' ', $classes ) . '"' : '' ?>><a href="{$url}"><img width="178" height="178"  src="<?= LinkUtility::GetPhotoThumb( $photo, true ) ?>" alt="{form:$photo.title}"></a></li>
            <? } ?>
        <? } elseif ( $a->Photo ) {  ?>
            <div class="present cont"><img height="640" src="<?= LinkUtility::GetPhotoHd( $a->Photo, true )?>" alt="" class="_background">
                <div class="_text">
                    <h1><a href="{$url}">{$a.title}</a></h1>
                    <p class="cFirm"><?= AlbumHelper::GetDate( $a ) ?>, {$a.Count()} фото<?= $a->description ? ', рассказ' : '' ?></p>
                    <? if ( $a->Tags ) { ?><p><?= TagHelper::GetTagLinks( $a->Tags )?> <? if ( $a->AllTags ) { ?><?= TagHelper::GetTagLinks( $a->AllTags )?><? } ?></p><? } ?>
                </div>
            </div>
        <? } ?>
        </ul>
    <? } ?>
            <? $pageCount = $__pageCount; $page = $__pageNumber; $pagesUrl = $as->GetPagesUrl(); ?>
            {increal:tmpl://fe/elements/paginator.tmpl.php}
        </div>
    </div>
</div>
{increal:tmpl://fe/elements/footer.tmpl.php}