<?php
    $breadcrumbs = Context::GetBreadcrumbs();
    if ( $breadcrumbs && count( $breadcrumbs ) > 1 ) {
        $totalBreadcrumbs = count( $breadcrumbs );
        $tempIndex        = 1;
        ?>
        <ul class="metaList _breadcrumbs fsSmall cont">
            <? foreach ( $breadcrumbs as $bc ) { ?>
                <? $link = $bc->GetLink(); ?>
                <li><? if ( $link !== '#' && $tempIndex !== $totalBreadcrumbs ) { ?><a href="{web:$link}">{$bc.title}</a><? } else { ?>{$bc.title}<? } ?></li>
                <? $tempIndex++ ?>
            <? } ?>
        </ul>
    <? } else { ?>
        <ul class="metaList _breadcrumbs fsSmall cont">
            <li>Главная</li>
        </ul>
    <? } ?>