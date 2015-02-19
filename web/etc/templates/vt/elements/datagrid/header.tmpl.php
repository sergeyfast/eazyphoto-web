<?php
    /** @var string $sortType */
    /** @var string $sortField */

    $grid['operations'] = !isset( $grid['operations'] ) ? true : $grid['operations']; 
    $grid['colspans']   = !isset( $grid['colspans'] ) ? [] : $grid['colspans'];
    $grid['deleteStr']  = !isset( $grid['deleteStr'] ) ? 'Do you want to delete this item?' : $grid['deleteStr'];
?>
<script>var objectDeleteStr = '{$grid[deleteStr]}', objectBasePath = '{$grid[basePath]}';</script>
<? if ( $grid["canPages"] ) { ?>
    <? $__paginatorMode = 'totals'; ?>
    {increal:tmpl://vt/elements/datagrid/paginator.tmpl.php}
<? } ?>
<div class="container _fluid">
    <table class="tableData">
    <thead>
        <tr>
            <?
                $currentIndex = 0;
                foreach ( $grid['columns'] as $title ) {
                    $colSpanStr    = ( isset( $grid['colspans'][$currentIndex] ) ) ? ' colspan="' . $grid['colspans'][$currentIndex] . '"' : '';
                    $headerClasses = [ ];
                    if( !empty( $grid['sorts'][$currentIndex] ) ) {
                        $field      = $grid['sorts'][$currentIndex];
                        $headerClasses += ['headerSort'];
                        if( $sortField == $field ) {
                            $headerClasses[] = $sortType == 'DESC' ? 'headerSortUp' : 'headerSortDown';
                        }
                    }
                    ?><th class="<?= implode( ' ', $headerClasses )?>" data-field="{$field}" {$colSpanStr}>{$title}</th><?
                    $currentIndex++;
                }
            ?>
            <? if ( $grid['operations'] ) { ?><th class="tableControls"></th><? } ?>
        </tr>
    </thead>
    <tbody>