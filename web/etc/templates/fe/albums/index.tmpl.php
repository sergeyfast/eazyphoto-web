<?php
    use Eaze\Helpers\ArrayHelper;

    /** @var Album[] $albums */
    /** @var Photo[] $photos */
    /** @var Tag[] $tagMap */
    /** @var Tag $tag */
?>
{increal:tmpl://fe/elements/header.tmpl.php}
<div class="container">
    {increal:tmpl://fe/elements/breadcrumbs.tmpl.php}
    <div class="row">
        <div class="col2">
            <? if ( false ) { ?>
            <h3 class="fsBigX">Год и месяц</h3>
            <p>
                <select style="max-width: 4.8em">
                    <option>2015</option>
                    <option>2014</option>
                    <option>2013</option>
                </select>
                <select style="max-width: 4.8em">
                    <option>Янв</option>
                    <option>Фев</option>
                    <option>Мрт</option>
                    <option>Снт</option>
                </select>
            </p>
            <h3 class="fsBigX">Рассказ</h3>
            <p>
                <label class="noWrap marginRightBase">
                    <input type="radio" name="ff03" checked> Есть
                </label>
                <label class="noWrap">
                    <input type="radio" name="ff03"> Нет
                </label>
            </p>
            <? } ?>
            <h3 class="fsBigX">Категории</h3>
            <ul class="flatList">
                <? foreach( $tagMap as $t ) { ?>
                    <? if ( $t->orderNumber && !$t->parentTagId ) { ?>
                <li<?= $tag && $tag->tagId === $t->tagId ? ' class="_active"' : '' ?>><a href="<?= LinkUtility::GetTagUrl( $t, true )?>">{$t.title}</a></li>
                    <? } ?>
                <? } ?>
            </ul>
            <h3 class="fsBigX">Теги</h3>
            <p><? foreach( $tagMap as $t ) { ?>
                <? if ( !$t->orderNumber ) { ?><a class="tag<?= $tag && $tag->tagId === $t->tagId ? ' _active' : '' ?>" href="<?= LinkUtility::GetTagUrl( $t, true )?>">{$t.title}</a> <? } ?>
            <? }?></p>
        </div>
        <div class="col10">
            <h1>Альбомы</h1>
            <? if ( false ) { ?>
            <ul class="metaList _nosep fsMedium">
                <li>Сортировать по:</li>
                <li><b>дате</b></li>
                <li><a href="#">названию</a></li>
                <li><a href="#">альбому</a></li>
            </ul>
            <? } ?>
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
            {increal:tmpl://fe/elements/paginator.tmpl.php}
        </div>
    </div>
</div>
{increal:tmpl://fe/elements/footer.tmpl.php}