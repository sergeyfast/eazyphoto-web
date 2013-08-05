<?php
    define( "INDEX_MODE", "index" );
    define( "SELECTOR_MODE", "selector" );

    /**
     * Base Tree Object Factory
     * 
     * @package Base
     * @subpackage Base.Tree
     * @author Rykin Maxim
     */
    class BaseTreeHelper {
        /**
         * Collapses objects to the tree.
         *
         * @param array $objects  List of the objects to collapse.
         */
        public static function Collapse( $objects ) {
            $tree = array();
            
            if ( empty( $objects ) ) {
                return array();
            }

            foreach ( $objects as $object ) {
                if ( empty( $objects[$object->parentId] ) ) {
                    $tree[$object->objectId] = $object;
                } else {
                    $objects[$object->parentId]->nodes[$object->objectId] = $object;
                    $object->parent = $objects[$object->parentId];
                }
            }
            
            return $tree;
        }

        /**
         * Renders Tree to the control
         *
         * @param array $objects  Array of root elements.
         * @param string $mode    Mode for render.
         */
        protected static function RenderToForm( $objects, $control, $field, $prefix, $mode = "index" ) {
            if ( !is_array( $objects ) ) {
                $objects = array( $objects );
            }
            
            echo '<ul id="' . $control . '" class="filetree">';

            foreach ( $objects as $object ) {
                self::drawElement( $object, $field, $prefix, $mode );
            }

            echo "</ul>";
            
            self::LoadScript( $control );
        }
        
        
        /**
         * Recursive Output For Tree Element
         *
         * @param array $objects
         * @param string $mode
         */
        private static function RecurciveTreeOuput( $objects, $field, $prefix, $mode = INDEX_MODE ) {
            if ( count( $objects ) !== 0 ) {
                echo "<ul>";
                    foreach ( $objects as $object ) {
                        self::drawElement( $object, $field, $prefix, $mode );
                    }
                echo "</ul>";
            }
        }
        
        /**
         * Draw A Tree Element.
         *
         * @param BaseTreeObject $object  Tree element to show.
         * @param string $field           Title field.
         * @param string $prefix          Prefix for the url.
         * @param string $mode            Mode for output.
         */
        private static function drawElement( $object, $field, $prefix, $mode = INDEX_MODE ) {
            switch ( $mode ) {
                case INDEX_MODE:
?>                        
                <li id="node_<?= $object->getFormattedPath() ?>"><span class="folder"><?= $object->$field ?></span>
                <span class="toolbox"><a href='<?= Site::GetWebPath( "vt://$prefix/add/{$object->path}" ) ?>'>add</a> <a href="<?= Site::GetWebPath( "vt://$prefix/edit/{$object->path}" ) ?>">edit</a> <a href="javascript:removeNode('<?= $object->getFormattedPath() ?>');">delete</a></span>
                <?php self::RecurciveTreeOuput( $object->nodes, $field, $prefix, $mode ) ?>
                </li>
<?php
                    break;
                case SELECTOR_MODE:
?>                        
                <li id="node_<?= $object->getFormattedPath() ?>">
                <span class="folder"><input type="radio" name="nodeId" value="<?= $object->path ?>" /><?= $object->$field ?></span>
                <?php self::RecurciveTreeOuput( $object->nodes, $field, $prefix, $mode ) ?>
                </li>
<?php                        
                    break;
            }
        }
        
        /**
         * Loads javascript for delete support.
         *
         * @param string $control  Control name.
         */
        private static function LoadScript( $control ) {
?>
    <script type="text/javascript">
		function removeNode( objectId ) {
            if ( confirm( 'delete?' ) ) {
                $( '#node_' + objectId ).hide();
                
                $.get( '<?= Site::GetWebPath( "vt://questions/rubrics/delete/" ) ?>' + objectId.replace( /_/g,"."), {}, function(data) {
                    $("#statusBar").html( "<b>{lang:vt.common.deleted}</b>").animate({opacity: 1}).animate({opacity: 0}, 300);
                } );
            }
        }
	</script>
<?php
        }
    }
?>