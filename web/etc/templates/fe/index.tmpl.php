<?php
    $__isMainPage = true;

    if ( !empty( $year ) ) {
        $__breadcrumbs = array( array( 'title' => $year, 'path' => LinkUtility::GetAlbumsUrl( $year ) ) );
    }

?>
{increal:tmpl://fe/elements/header.tmpl.php}
{increal:tmpl://fe/elements/breadcrumbs.tmpl.php}
{increal:tmpl://fe/elements/albums.tmpl.php}
{increal:tmpl://fe/elements/footer.tmpl.php}