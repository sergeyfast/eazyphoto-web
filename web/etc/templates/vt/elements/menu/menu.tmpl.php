<?
    use Eaze\Helpers\ArrayHelper;
    use Eaze\Helpers\FormHelper;
    use Eaze\Site\Site;

    $__activeElement = !empty( $__activeElement ) ? $__activeElement : '';

    /**
     * @param        $menuKey
     * @param        $menuElement
     * @param        $__activeElement
     * @param string $__menuHTML
     */
    function renderMenuElement( $menuKey, $menuElement, $__activeElement, &$__menuHTML = '' ) {
        $activeClass = $__activeElement === $menuKey ? '_active' : null;
        $__menuHTML .= !empty( $menuElement['menu'] ) ? '<li class="_dropDown ' . $activeClass . '">' : ( $activeClass ? '<li class="' . $activeClass . '">' : '<li>' );
        $__menuHTML .= FormHelper::FormLink(
            ( empty( $menuElement['link'] ) ) ? "#" : Site::GetWebPath( $menuElement['link'] )
            , T( $menuElement['title'] )
            , null
            , null
            , [ 'target' => ArrayHelper::GetValue( $menuElement, 'target', null ) ]
        );

        if ( !empty( $menuElement['menu'] ) ) {
            $__menuHTML .= '<ul>';
            foreach ( $menuElement['menu'] as $subMenuElement ) {
                renderMenuElement( null, $subMenuElement, $__activeElement, $__menuHTML );
            }
            $__menuHTML .= '</ul>';
        }

        $__menuHTML .= '</li>';
    }

    // Render Menu
    if ( !empty( $__menu ) ) {
        $__menuHTML = '';
        foreach ( $__menu as $menuKey => $menuElement ) {
            renderMenuElement( $menuKey, $menuElement, $__activeElement, $__menuHTML );
        }
        ?>
        <nav role="navigation">
            <div class="container">
                <ul>{$__menuHTML}</ul>
            </div>
        </nav>
    <? } ?>