<!-- menu -->
<?
    if ( empty( $__activeElement ) ) {
        $__activeElement = "";
    }

    function renderMenuElement( $menuKey, $menuElement, $__activeElement, &$__menuHTML = '' ) {
        $__menuHTML .= '<li>';
        $__menuHTML .= sprintf( '<a %s %s href="%s">%s</a>'
            , ( !empty( $menuElement['target'] ) ) ? 'target="' . $menuElement['target'] . '"' : ''
            , ( $__activeElement === $menuKey ) ? 'class="active" ' : ''
            , ( ( empty($menuElement["link"]) ) ? "#" : Site::GetWebPath( $menuElement["link"] ) )
            , LocaleLoader::Translate( $menuElement['title'] )
        );

            if ( !empty( $menuElement["menu"] ) ) {
                $__menuHTML .= '<ul>';
                foreach( $menuElement["menu"] as $subMenuElement ) {
                    renderMenuElement( null, $subMenuElement, $__activeElement, $__menuHTML );
                }
                $__menuHTML .= '</ul>';
            }

        $__menuHTML .= '</li>';
    }

    if( !empty( $__menu ) ) {
        ?>
        <div class="header">
            <div class="inner">
                <ul class="menu header-menu">
                <?

                $__menuHTML = '';
                foreach ( $__menu as $menuKey => $menuElement ) {
                    renderMenuElement( $menuKey, $menuElement, $__activeElement, $__menuHTML );
                }
                echo $__menuHTML;

                ?>
                </div>
            </div>
        </ul>
        <?
    }
?>
<!-- menu -->