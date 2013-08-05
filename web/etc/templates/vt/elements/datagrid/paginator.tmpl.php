<?
    $page++;
    $pageCount = ceil( $pageCount );
?>
<div class="paginator">
    <? if( $pageCount > 1 ) { ?>
    <div class="paginator-pages">
        <? if( $page != 1 ) { ?>
        <a class="prev" href="#">{lang:vt.grid.pPrev}</a>
        <? } ?>
        <? if( $pageCount != 1 ) { ?>
        <a class="next" href="#">{lang:vt.grid.pNext}</a>
        <? } ?>
        <label><input type="text" value="{$page}" /> {lang:vt.grid.pFrom} {$pageCount}</label>
    </div>
    <? } ?>

    <? if( !empty( $__paginatorMode ) && ( $objectCount > 0 ) ) { ?>

        <?
            if( $__paginatorMode == 'pageSizes' ) {
                ?>
                <div class="paginator-total">
                    <div class="paginator-total">{lang:vt.grid.pageSize}
                        <ul class="paginator-sizes">
                            <?
                                $pageSizes = array( 10, 25, 50, 100, 500 );
                                foreach ( $pageSizes as $value ) {
                                    ?><li <?= ( $value == $grid["pageSize"] ) ? 'class="active"' : '' ?>><a href="#" data-value="{$value}">{$value}</a></li><?
                                }
                            ?>
                        </ul>
                    </div>
                </div>
                <?

            } else if( $__paginatorMode == 'totals' ) {
                $__firstRange   = ( $page - 1 ) * $grid["pageSize"] + 1;
                $__secondRange  = $__firstRange + count( $list ) - 1;
                $__declension   = TextHelper::GetDeclension( $objectCount );
                if( $pageCount == 1 ) {
                    ?><div class="paginator-total"><?= sprintf( LocaleLoader::Translate( 'vt.grid.resultsCount.onePage' . $__declension ), $objectCount ) ?></div><?
                } else {
                    ?><div class="paginator-total"><?= sprintf( LocaleLoader::Translate( 'vt.grid.resultsCount.pages' ), $__firstRange, $__secondRange, number_format( $objectCount,  0, "", " " ) ) ?></div><?
                }
            }
        ?>
    <? } ?>
</div>
<?
    $page--;
?>