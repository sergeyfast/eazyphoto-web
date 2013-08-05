<?php
    /**
     * Tree Factory Abstract Class.
     *
     * @package Base
     * @subpackage Base.Tree
     * @author Rykin Maxim
     */
    abstract class TreeFactory implements ITreeFactory {
        /**
         * Gets results form the data set.
         *
         * @param IDataSet $ds    Result Data Set.
         * @param array $options  Array of options.
         * @static
         * @access private
         * @return array
         */
        public static function GetResults( DataSet $ds, $options = array(), $mapping, $connectionName = null ) {
            $structure = BaseFactory::GetObjectTree( $ds->Columns );
            $result    = array();
            $keys      = BaseFactoryPrepare::GetPrimaryKeys( $mapping );
            $key       = ( empty( $keys[0] ) ) ? null : $keys[0];

            while ( $ds->next() ) {
                if ( !empty($structure[$key])) {
                    $result[$ds->getParameter( $key )] = BaseFactory::getObject( $ds, $mapping, $structure );
                } else {
                    $result[] = self::getObject( $ds, $mapping, $structure );
                }
            }

            // With Lists Mode
            if ( !empty( $options[BaseFactory::WithLists] )
                    && !empty( $mapping["lists"] )
                    && !empty( $result ) ) {
                foreach ( $mapping["lists"] as $name => $value ) {
                    $ids         = array_keys( $result );
                    $factoryName = $value["foreignKey"] . "Factory";
                    $factory     = new $factoryName();
                    $listArray = $factory->Get( array( "_" . $value["name"] => $ids ), null, $connectionName );

                    BaseFactoryPrepare::Glue( $result, $listArray, $value["name"], $name );
                }
            }

            return $result;
        }
    }
?>