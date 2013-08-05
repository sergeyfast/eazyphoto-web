<?php
    if ( !empty($__page) ) {
        $__pageTitle       = !empty($__page->pageTitle) ? $__page->pageTitle : $__page->title;
        $__metaDescription = $__page->metaDescription;
        $__metaKeywords    = $__page->metaKeywords;
    }
?>
{increal:tmpl://fe/elements/header.tmpl.php}
<h1>{$__page.title}</h1>
{$__page.content}
{increal:tmpl://fe/elements/footer.tmpl.php}