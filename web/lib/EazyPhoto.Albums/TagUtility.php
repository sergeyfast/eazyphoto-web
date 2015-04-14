<?php
    use Eaze\Database\ConnectionFactory;
    use Eaze\Database\SqlCommand;
    use Eaze\Model\BaseFactory;
    use Eaze\Model\FactoryWrapper;


    /**
     * Tag Utility
     * @package EazyPhoto
     * @subpackage Albums
     * @author sergeyfast
     */
    class TagUtility {

        /**
         * Get All Tags with path
         * @return Tag[]
         */
        public static function GetAllTags() {
            $sql = <<<sql
            WITH RECURSIVE search_tags( "tagId", "parentTagId", depth, path, cycle) AS (
                  SELECT t."tagId", t."parentTagId", 1, ARRAY[t."tagId"], FALSE
                  FROM tags t
                  UNION ALL
                  SELECT t."tagId", t."parentTagId", st.depth + 1, path || t."tagId", t."tagId" = ANY(path)
                  FROM tags t, search_tags st
                  WHERE st."tagId" = t."parentTagId" AND NOT cycle
            )
            SELECT t.*, st."path", st."depth"
            FROM search_tags st
                    INNER JOIN "getTags" t USING ( "tagId" )
            WHERE t."statusId" = 1
            ORDER BY depth, "orderNumber", "title";
sql;

            $cmd = new SqlCommand( $sql, ConnectionFactory::Get() );
            $fw  = ( new FactoryWrapper( 'Tag' ) )
                ->SetField( 'path', [ 'type' => TYPE_ARRAY, 'complexType' => \Eaze\Database\PgSql\PgSqlTypeIntArray::GetName() ] )
                ->SetField( 'depth', [ 'type' => TYPE_INTEGER, ] );

            $list = TagFactory::Get( [ ], [ BaseFactory::CustomSqlCommand => $cmd ] );
            unset ( $fw );

            return $list;
        }


        /**
         * Get All Parent & Child Tags for specific parentTagId
         * @param Tag[] $map
         * @param int   $tagId main tag id
         * @return Tag[]
         */
        public static function FilterTags( $map, $tagId ) {
            $result = [];

            foreach( $map as $t ) {
                if ( $t->path && in_array( $tagId, $t->path, true ) ) {
                    $result[$t->tagId] = $t->tagId;
                }
            }

            return $result;
        }
    }