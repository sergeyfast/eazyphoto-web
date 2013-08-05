<? if( !empty( $__breadcrumbs ) ) { ?>
    <? if ( !isset( $__breadcrumbRootPath ) ) { $__breadcrumbRootPath = 'vt://'; } ?>
    <ul class="breadcrambs">
        <li><a href="{web:$__breadcrumbRootPath}">{lang:vt.common.mainPage}</li>
    <? foreach( $__breadcrumbs as $__breadcrumb ) { ?>
        <li><a href="{$__breadcrumb[link]}">{$__breadcrumb[title]}</a></li>
    <? } ?>
    </ul>
<? } ?>