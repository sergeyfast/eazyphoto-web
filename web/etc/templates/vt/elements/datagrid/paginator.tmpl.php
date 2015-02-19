<?
    /** @var int $page */
    /** @var int $objectCount */
    /** @var array $grid */
    /** @var array $list  */

    use Eaze\Helpers\TextHelper;

    $page ++;
    $pageCount        = ceil( $pageCount );
    $__paginationMode = !empty( $__paginatorMode ) ? $__paginatorMode : '';
?>
<? if ( $__paginationMode && $objectCount > 0 ) { ?>
<div class="container fsMedium">
    <div class="row">
        <div class="col6">
            <? if ( $__paginationMode == 'pageSizes' ) { ?>
            <ul class="metaList _nosep _tight" id="pageSize-changer">
                <li>{lang:vt.grid.pageSize}</li>
                <? $pageSizes = [ 10, 25, 50, 100, 500 ]; ?>
                <? foreach ( $pageSizes as $value ) { ?>
                    <? if ( $value == $grid['pageSize'] ) { ?>
                <li><strong class="cFirmC">{$value}</strong></li>
                    <? } else { ?>
                <li><a href="#" data-value="{$value}">{$value}</a></li>
                    <? } ?>
                <? } ?>
            </ul>
            <? } else if ( $__paginatorMode == 'totals' ) { ?>
            <?
                $__firstRange   = ( $page - 1 ) * $grid["pageSize"] + 1;
                $__secondRange  = $__firstRange + count( $list ) - 1;
                $__declension   = TextHelper::GetDeclension( $objectCount );
                if( $pageCount == 1 ) {
            ?>
            <p class="cFade"><?= T( 'vt.grid.resultsCount.onePage' . $__declension , $objectCount ) ?></p>
                <? } else { ?>
            <p class="cFade"><?= sprintf( T( 'vt.grid.resultsCount.pages' ), $__firstRange, $__secondRange, number_format( $objectCount,  0, '', ' ' ) ) ?></p>
                <? } ?>
            <? } ?>
        </div>
        <? if( $pageCount > 1 ) { ?>
        <div class="col6 alignRight">
            <ul class="metaList _nosep _tight marginTopButtonEm flatBottom page-changer">
                <? if( $page != 1 ) { ?>
                <li><a href="#" rel="prev" class="prev button _light">« {lang:vt.grid.pPrev}</a></li>
                <? } ?>
                <li>
                    <input type="number" min="1" max="{$pageCount}" value="{$page}" class="alignRight">
                </li>
                <li>{lang:vt.grid.pFrom} {num:$pageCount}</li>
                <? if( $pageCount != 1 ) { ?>
                <li><a href="#" rel="next" class="next button _light">{lang:vt.grid.pNext} »</a></li>
                <? } ?>
            </ul>
        </div>
        <? } ?>
    </div>
</div>
<? } ?>
<? $page--; ?>