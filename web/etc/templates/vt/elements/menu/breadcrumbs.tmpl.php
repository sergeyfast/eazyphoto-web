<? if( !empty( $__breadcrumbs ) ) { ?>
    <? if ( !isset( $__breadcrumbRootPath ) ) { $__breadcrumbRootPath = 'vt://'; } ?>
    <div class="container">
        <ul class="metaList _breadcrumbs fsSmall cont">
        <li><a href="{web:$__breadcrumbRootPath}">{lang:vt.common.mainPage}</li>
        <? foreach( $__breadcrumbs as $__breadcrumb ) { ?>
            <? if ( !empty( $__breadcrumb['link'] ) ) { ?>
            <li><a href="{$__breadcrumb[link]}">{$__breadcrumb[title]}</a></li>
            <? } else { ?>
            <li>{$__breadcrumb[title]}</li>
            <? } ?>
        <? } ?>
        </ul>
    </div>
<? } ?>