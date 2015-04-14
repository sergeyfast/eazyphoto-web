<?php
    /** @var SiteParamHelper $sph */

    use Eaze\Helpers\CssHelper;
    use Eaze\Helpers\JsHelper;
    use Eaze\Modules\LocaleLoader;
    use Eaze\Site\Site;

    $__isMainPage = Context::GetUrl() === '/';
    $__metaDetail = Context::$MetaDetail;
    $__ogImage    = !empty( $__ogImage ) ? $__ogImage : null;

    /** Manual set meta or reset of meta */
    $__sitePageTitle    = $sph->GetSiteHeader();
    $__pageTitle        = !empty( $__pageTitle ) ? $__pageTitle : '';
    $__metaDescription  = !empty( $__metaDescription ) ? $__metaDescription : '';
    $__metaKeywords     = !empty( $__metaKeywords ) ? $__metaKeywords : '';
    $__imageAlt         = !empty( $__imageAlt ) ? $__imageAlt : '';
    $__canonicalUrl     = !empty( $__canonicalUrl ) ? $__canonicalUrl : '';

    /** Priority: meta, page, variables */
    if ( $__metaDetail ) {
        $__pageTitle       = $__metaDetail->pageTitle ?: $__pageTitle;
        $__metaDescription = $__metaDetail->metaDescription ?: $__metaDescription;
        $__metaKeywords    = $__metaDetail->metaKeywords ?: $__metaKeywords;
        $__imageAlt        = $__metaDetail->alt ?: $__imageAlt;
        $__canonicalUrl    = $__metaDetail->canonicalUrl ?: $__canonicalUrl;
    } else if( !empty( $__page ) ) {
        $__pageTitle = $__page->title . ' | ' . $__sitePageTitle;
    }

    /** Default page title */
    $__pageTitle = $__pageTitle ?: $__sitePageTitle;

    CssHelper::Init( !Site::IsDevel() );
    JsHelper::Init( !Site::IsDevel() );

    CssHelper::PushFiles([
        'css://styles.css',
        'css://fancybox/jquery.fancybox.css',
    ]);

    JsHelper::PushFiles([
        'js://fe/locale/'. LocaleLoader::$CurrentLanguage . '.js',
        'js://fe/jquery.js',
        'js://fe/jquery.easing.js',
        'js://fe/jquery.fancybox.js',
    ]);
?><!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>{$__pageTitle}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="{form:$__metaKeywords}" />
    <meta name="description" content="{form:$__metaDescription}" />
    <? if (  $__ogImage ) { ?><meta property="og:image" content="{$__ogImage}" /><? } ?>
    <? if ( $__isMainPage ) { ?>
    <? if ( $sph->HasYandexMeta() ) { ?><meta name="yandex-verification" content="{$sph.GetYandexMeta()}" /><? } ?>
    <? if ( $sph->HasGoogleMeta() ) { ?><meta name="google-site-verification" content="{$sph.GetGoogleMeta()}" /><? } ?>
    <? if ( $sph->HasBingMeta() ) { ?><meta name="msvalidate.01" content="<?= $sph->GetBingMeta() ?>" /><? } ?>
    <? } ?>
    <?= CssHelper::Flush(); ?>
    <link rel="shortcut icon" href="{web:/favicon.ico}">
    <script>var root = '{web:/}';</script>
</head>
<body>
<div class="wrapper">
    <header role="banner">
        <div class="container">
            <div class="row">
                <div class="col3 headerLogo"><? $ct = ''; if ( !$__isMainPage ) { $ct = '</a>'; ?><a href="{web:/}"><? } ?><img src="{web:img://}logo.png" alt="<?= $sph->GetSiteHeader() ?>">{$ct}</div>
                <nav role="navigation" class="col8">
                    <? if ( Context::$HeaderNav ) { ?>
                        <ul class="metaList">
                            <? foreach( Context::$HeaderNav as $n ) { ?>
                                <li<?= Context::$Navigation && $n->navigationId === Context::$Navigation->navigationId ? ' class="_active"' : '' ?>><a href="{$n.GetLink(true)}"><span>{$n.title}</span></a></li>
                            <? } ?>
                        </ul>
                    <? } ?>
                </nav>
            </div>
        </div>
    </header>