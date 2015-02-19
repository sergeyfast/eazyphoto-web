<?php
    use Eaze\Helpers\CssHelper;
    use Eaze\Helpers\JsHelper;
    use Eaze\Modules\LocaleLoader;

    $__noMenu      = !empty( $__noMenu );
    $__breadcrumbs = !empty( $__breadcrumbs ) ? $__breadcrumbs : [ ];
    $__currentLang = LocaleLoader::$CurrentLanguage;
    $__pageTitle   = empty( $__pageTitle ) ? T( 'vt.common.title' ) : $__pageTitle;
    $__projectName = 'eazyphoto';
    $__User        = !empty( $__User ) ? $__User : null;


    /** Menu Structure */
    $__menu = [
        'albums' => [
            'title' => 'vt.menu.albums',
            'link'  => 'vt://albums/',
        ],
        'photos' => [
            'title' => 'vt.menu.photos',
            'link'  => 'vt://photos/',
        ],
        'static-pages' => [
            'title' => 'vt.menu.staticPages',
            'link'  => 'vt://static-pages/',
            'menu'  => [
                [ 'title' => 'vt.menu.navigations', 'link' => 'vt://navigations/' ],
                [ 'title' => 'vt.menu.navigationTypes', 'link' => 'vt://navigations/types/' ],
                [ 'title' => 'vt.menu.metaDetails', 'link' => 'vt://meta-details/' ],
            ]
        ],
        'site-params'  => [
            'title' => 'vt.menu.siteParams',
            'link'  => 'vt://site-params/',
            'menu'  => [
                [ 'title' => 'vt.menu.users', 'link' => 'vt://users/' ],
                [ 'title' => 'vt.menu.vfs', 'link' => 'vt://vfs/' ],
                [ 'title' => 'vt.menu.daemons', 'link' => 'vt://daemons/' ],
            ]
        ],
    ];


    CssHelper::Init( true );
    CssHelper::PushFiles(
        [
            'css://vt/general_foundicons.css',
            'js://ext/jquery.ui/jquery-ui.css',
            'js://ext/jquery.ui/jquery-ui.theme.css',
            'js://ext/select2/select2.css',
            'css://vt/styles.css',
            'css://vt/fancybox/jquery.fancybox.css',
        ]
    );

    JsHelper::Init( true );
    JsHelper::PushFiles(
        [
            'js://ext/jquery/jquery.js',
            'js://ext/jquery/jquery.easing.js',
            'js://ext/jquery/jquery.fancybox.js',
            'js://ext/jquery/jquery.tablesorter.min.js',
            'js://ext/jquery/jquery.datetimepicker.js',
            'js://ext/jquery/jquery.maskedinput.js',
            'js://vfs/vfsConstants.' . $__currentLang . '.js',
            'js://vfs/vfs.selector.js',
            'js://vt/locale/' . $__currentLang . '.js',
            'js://ext/select2/select2.min.js',
            'js://ext/jquery.ui/jquery-ui.min.js',
            'js://ext/jquery.ui/jquery.ui.datepicker-ru.js',
            'js://vt/datagrid.js',
        ]
    );

    if ( !empty( $cssFilesAdds ) ) {
        CssHelper::PushGroups( $cssFilesAdds );
    }

    if ( !empty( $jsFilesAdds ) ) {
        JsHelper::PushFiles( $jsFilesAdds );
    }
?><!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title>{$__pageTitle}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?= CssHelper::Flush() ?>
        <link rel="shortcut icon" href="{web:/}favicon.ico">
        <script>var root = '{web:/}', controlsRoot = '{web:controls://}';</script>
        <?= JsHelper::Flush(); ?>
    </head>
<body>
<div class="wrapper">
    <header role="banner">
        <div class="container">
            <div class="row">
                <div class="col4 headerLogo">
                    <p>{$__projectName}</p>
                </div>
                <div class="col8 alignRight">
                    <? if ( $__User ) { ?>
                        <ul class="metaList">
                            <li>{$__User.login}</a></li>
                            <li><a href="{web:/}" target="_blank">Открыть сайт</a></li>
                            <li><a href="{web:vt://login}">Выход</a></li>
                        </ul>
                    <? } ?>
                </div>
            </div>
        </div>
    </header>
<? if ( !$__noMenu ) { ?>{increal:tmpl://vt/elements/menu/menu.tmpl.php}<? } ?>