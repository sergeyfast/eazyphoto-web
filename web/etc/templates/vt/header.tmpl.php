<?php
    if ( empty( $__noMenu ) ) {
        $__noMenu = false;
    }

    $__breadcrumbs = !empty( $__breadcrumbs ) ? $__breadcrumbs : array();

    $__currentLang = ( class_exists( "LocaleLoader" ) ) ? LocaleLoader::$CurrentLanguage : "ru";

    if ( empty( $__pageTitle ) ) {
        $__pageTitle = ( class_exists( "LocaleLoader" ) ) ? LocaleLoader::Translate( "vt.common.title" ) : "Virtual Terminal";
    }

    /** Menu Structure */
    $__menu = array(
        "albums" => array(
            "title"  => "vt.menu.albums"
            , "link" => "vt://albums/"
        )
        , "photos" => array(
            "title"  => "vt.menu.photos"
            , "link" => "vt://photos/"
        )
        , "static-pages" => array(
            "title"  => "vt.menu.staticPages"
            , "link" => "vt://static-pages/"
            , "menu" => array(
                array(
                    "title"  => "vt.menu.navigations"
                    , "link" => "vt://navigations/"
                )
                , array(
                    "title"  => "vt.menu.navigationTypes"
                    , "link" => "vt://navigations/types/"
                )
                , array(
                    "title"  => "vt.menu.metaDetails"
                    , "link" => "vt://meta-details/"
                )
            )
        )
        , "site-params" => array(
            "title"  => "vt.menu.siteParams"
            , "link" => "vt://site-params/"
            , "menu" => array(
                array(
                    "title"  => "vt.menu.users"
                    , "link" => "vt://users/"
                )
                , array(
                    "title"  => "vt.menu.vfs"
                    , "link" => "vt://vfs/"
                )
            )            
        )
        , "exit" => array (
            "title"  => "vt.menu.exit"
            , "link" => "vt://login"
            , "menu" => array(
                array(
                    "title"  => "vt.menu.logout"
                    , "link" => "vt://login"
                )
                , array(
                    "title"  => "vt.menu.toSite"
                    , "link" => "/"
                )
                , array(
                    "title"    => "vt.menu.toSiteNew"
                    , "link"   => "/"
                    , "target" => "_blank"
                )
            )
        )
    );

    $cssFiles = array(
        AssetHelper::AnyBrowser => array(
            'css://vt/common.css'
            , 'css://vt/tags.css'
            , 'css://vt/classes.css'
            , 'css://vt/layout.css'
            , 'css://vt/ui.css'
            , 'css://vt/custom.css'
            , 'css://vt/legend.css'

            , 'js://ext/fancybox/jquery.fancybox-1.3.4.css'
        )
        , AssetHelper::IE7 => array(
            'css://vt/common-ie.css'
            , 'css://vt/tags-ie.css'
            , 'css://vt/classes-ie.css'
            , 'css://vt/layout-ie.css'
        )
    );

    $jsFiles = array(
        'js://ext/jquery/jquery.js'
        , 'js://ext/jquery.plugins/jquery.superfish.js'
        , 'js://ext/jquery.plugins/jquery.clearable.js'
        , 'js://ext/jquery.plugins/jquery.datetimepicker.js'
        , 'js://ext/jquery.plugins/jquery.maskedinput.js'
        , 'js://ext/jquery.plugins/jquery.confirmdialog.js'
        , 'js://ext/jquery.plugins/jquery.blockui.js'
        , 'js://ext/jquery.plugins/jquery.cookie.js'
        , 'js://ext/jquery.ui/jquery-ui.js'

        , 'js://ext/fancybox/jquery.easing-1.3.pack.js'
        , 'js://ext/fancybox/jquery.fancybox-1.3.4.js'

        , 'js://vfs/vfsConstants.'. $__currentLang . '.js'

        , 'js://vt/locale/'. $__currentLang . '.js'
        , 'js://vt/script.js'
        , 'js://vt/translit-alias.js'
    );

    CssHelper::Init( true );
    JsHelper::Init( true );

    CssHelper::PushGroups( $cssFiles );
    if( !empty( $cssFilesAdds ) ) {
        CssHelper::PushGroups( $cssFilesAdds );
    }

    JsHelper::PushFiles( $jsFiles );
    if( !empty( $jsFilesAdds ) ) {
        JsHelper::PushFiles( $jsFilesAdds );
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id="nojs">
<head>
	<title><?= $__pageTitle ?></title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
	<meta http-equiv="Content-Type" content="text/html; charset=<?= LocaleLoader::$HtmlEncoding ?>" />

    <script type="text/javascript">
        document.documentElement.id = "js";
        var root = '{web:/}';
        var controlsRoot = '{web:controls://}';
    </script>
    <?= CssHelper::Flush(); ?>
    <?= JsHelper::Flush(); ?>
</head>
<body>
    <? if ( !$__noMenu ) { ?>
        {increal:tmpl://vt/elements/menu/menu.tmpl.php}
    <? } ?>