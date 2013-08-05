<?php
    /** @var SiteParamHelper $sph */

    if ( !isset( $__activeElement ) ) {
        $__activeElement = NULL;
    }

    if ( !isset( $__isMainPage ) ) {
        $__isMainPage = false;
    }

    /**
     * Manual set meta or reset of meta
     */
    $__sitePageTitle    = $sph->GetSiteHeader();
    $__pageTitle        = !empty( $__pageTitle ) ? $__pageTitle : '';
    $__metaDescription  = !empty( $__metaDescription ) ? $__metaDescription : '';
    $__metaKeywords     = !empty( $__metaKeywords ) ? $__metaKeywords : '';
    $__imageAlt         = !empty( $__imageAlt ) ? $__imageAlt : '';

	/*
	 * Meta tags from MetaDetail object or Page object
	*/
	if ( !empty( $__metaDetail ) ) {
        if ( !empty( $__metaDetail->pageTitle ) )       $__pageTitle       = $__metaDetail->pageTitle;
        if ( !empty( $__metaDetail->metaDescription ) ) $__metaDescription = $__metaDetail->metaDescription;
        if ( !empty( $__metaDetail->metaKeywords) )     $__metaKeywords    = $__metaDetail->metaKeywords;
        if ( !empty( $__metaDetail->alt) )         		$__imageAlt        = $__metaDetail->alt;
    } else if( !empty( $__page ) ) {
        $__pageTitle = !empty( $__page->pageTitle ) ? $__page->pageTitle : ( $__page->title . ' | ' . $__sitePageTitle );
        
        if ( !empty( $__page->metaDescription ) ) $__metaDescription = $__page->metaDescription;
        if ( !empty( $__page->metaKeywords) )     $__metaKeywords    = $__page->metaKeywords;
    }

    /**
     * Default page title
     */
    $__pageTitle = !empty( $__pageTitle ) ? $__pageTitle : $__sitePageTitle;
	
    $cssFiles = array(
        AssetHelper::AnyBrowser => array(
            'css://fe/foundation.min.css'
            , 'css://fe/custom.css'
        )
        , AssetHelper::IE7 => array()
    );

    $jsFiles = array(
        'js://fe/modernizr.foundation.js'
        , 'js://fe/jquery.js'
        //, 'js://fe/foundation.min.js'
        , 'js://fe/scripts.js'
    );

    CssHelper::Init( !Site::IsDevel() );
    JsHelper::Init( !Site::IsDevel() );

    CssHelper::PushGroups( $cssFiles );
    JsHelper::PushFiles( $jsFiles );
?>
<!DOCTYPE html>
<!--[if IE 8]>
<html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width"/>
    <title><?=$__pageTitle?></title>
    <meta name="keywords" content="{form:$__metaKeywords}" />
    <meta name="description" content="{form:$__metaDescription}" />
    <? if ( $__isMainPage ) { ?>
        <? if (!empty( $__params[SiteParamHelper::YandexMeta] ) ) { ?>
            <meta name='yandex-verification' content='<?= $__params[SiteParamHelper::YandexMeta]->value ?>' />
        <? } ?>
        <? if (!empty( $__params[SiteParamHelper::GoogleMeta] ) ) { ?>
            <meta name='google-site-verification' content='<?= $__params[SiteParamHelper::GoogleMeta]->value ?>' />
        <? } ?>
    <? } ?>
    <link rel="icon" href="{web:/favicon.ico}" type="image/x-icon" />
    <link rel="shortcut icon" href="{web:/favicon.ico}" type="image/x-icon" />
    <?= CssHelper::Flush(); ?>
    <script type="text/javascript">
        document.documentElement.id = "js";
        var root = '{web:/}';
        var controlsRoot = '{web:controls://}';
    </script>
</head>
<body>
<nav class="top-bar">
    <ul class="title-area">
        <li class="name">
            <h1><a href="{web:/}"><?= $sph->GetSiteHeader() ?></a></h1>
        </li>
    </ul>
</nav>