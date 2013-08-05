<?php
    // add default path to breadcrumbs
    if ( isset( $__breadcrumbs ) ) {
        array_unshift( $__breadcrumbs, array( 'title' => 'Главная', 'path' => '/' ) );
    }

    // Create breadcrumbs array for view
    $tCount = 0;
    $i = 1;
    if ( !empty( $__breadcrumbs ) ) {
        $tCount = count( $__breadcrumbs );
        foreach ( $__breadcrumbs as $breadcrumb ) {
            $__breadcrumbsArr[] = ( !empty( $breadcrumb['path'] ) ) ? FormHelper::FormLink( Site::GetWebPath( $breadcrumb['path'] ), $breadcrumb['title'] ) : $breadcrumb['title'];
        }
    }

    // render breadcrumbs
    if ( !empty( $__breadcrumbsArr ) ) {
        ?>
        <div class="row">
            <ul class="breadcrumbs">
                <? foreach ( $__breadcrumbsArr as $tLink ) { ?>
                    <li<?= $tCount == $i++ ? ' class="current"' : '' ?>>{$tLink}</li>
                <? } ?>
            </ul>
        </div>
    <? } ?>