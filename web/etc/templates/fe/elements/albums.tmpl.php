<?
    /** @var Album[] $albums */
    use Eaze\Helpers\ArrayHelper;

    /** @var Photo[] $photos */
    foreach ( $albums as $album ) {
        if ( !$album->metaInfo || empty( $album->metaInfo['count'] ) || empty( $album->metaInfo['photoIds'] ) ) {
            continue;
        }
        $url = LinkUtility::GetAlbumUrl( $album, true );
        ?>
        <div class="row">
            <h3><a href="{$url}">{$album.title}</a>
                <small><?= AlbumHelper::GetDate( $album ) ?>, <?= $album->metaInfo['count'] ?> фото<?= $album->isPrivate ? ', приватный ' : '' ?></small>
            </h3>
            <div class="large-12 columns">
                <ul class="small-block-grid-3 large-block-grid-6">
                    <? foreach ( $album->metaInfo['photoIds'] as $photoId ) { ?>
                        <? $photo = ArrayHelper::GetValue( $photos, $photoId ); ?>
                        <? if ( !$photo ) {
                            continue;
                        } ?>
                        <li><a href="{$url}"><img src="<?= LinkUtility::GetPhotoThumb( $photo, true ) ?>" alt="{form:$photo.title}"></a></li>
                    <? } ?>
                </ul>
            </div>
        </div>
        <hr/>
    <? } ?>