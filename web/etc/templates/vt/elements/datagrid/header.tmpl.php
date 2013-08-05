<?php
    $grid['operations'] = !isset( $grid['operations'] ) ? true : $grid['operations']; 
    $grid['colspans']   = !isset( $grid['colspans'] ) ? array() : $grid['colspans'];
    $grid['deleteStr']  = !isset( $grid['deleteStr'] ) ? 'Do you want to delete this item?' : $grid['deleteStr'];
?>
<script type="text/javascript">
    var objectDeleteStr = '{$grid[deleteStr]}';
    var objectBasePath = '{$grid[basepath]}';
</script>
<?
    if ( $grid["canPages"] ) {
        $__paginatorMode = 'totals';
        ?>{increal:tmpl://vt/elements/datagrid/paginator.tmpl.php}<?
    }
?>

<table class="objects">
    <thead>
        <tr>
            <?
                $currentIndex = 0;
                foreach ( $grid['columns'] as $title ) {
                    $sortStr = '';
                    if( !empty( $grid['sorts'][$currentIndex] ) ) {
                        $field      = $grid['sorts'][$currentIndex];
                        $sortClass  = 'sorted header';
                        if( $sortField == $field ) {
                            $sortClass .= ' ' . ( ( $sortType == 'DESC' ) ? 'headerSortUp' : 'headerSortDown' );
                        }
                        $sortStr    = ' class="' . $sortClass . '" data-field="' . $field . '"';
                    }
                    $colSpanStr = ( isset( $grid['colspans'][$currentIndex] ) ) ? ' colspan="' . $grid['colspans'][$currentIndex] . '"' : '';
                    ?><th{$sortStr}{$colSpanStr}><span>{$title}</span></th><?
                    $currentIndex++;
                }
            ?>
            <? if ( $grid['operations'] ) { ?><th></th><? } ?>
        </tr>
    </thead>
    <tbody>