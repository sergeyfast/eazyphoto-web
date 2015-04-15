<?php
    use Eaze\Database\ConnectionFactory;
    use Eaze\Database\SqlCommand;
    use Eaze\Helpers\ArrayHelper;
    use Eaze\Model\BaseFactory;
    use Eaze\Model\FactoryWrapper;


    /**
     * Tag Utility
     * @package    EazyPhoto
     * @subpackage Albums
     * @author     sergeyfast
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
         * Get Albums By Tags
         * @param Tag[] $map
         * @param Tag[] $tags $tagMap
         * @return AlbumByTag[]
         */
        public static function GetAlbumsByTags( $map, $tags ) {
            $conn    = ConnectionFactory::Get();
            $result  = [ ];
            $tagCond = '';
            $intArr  = $conn->GetComplexType( 'int[]' );
            foreach ( $tags as $t ) {
                $all = self::FilterTags( $map, $t->tagId );
                $tagCond .= sprintf( ' WHEN "tagIds" && %s THEN %d ' . PHP_EOL, $intArr->ToDatabase( array_keys( $all ) ), $t->tagId );
            }

            if ( !$tagCond ) {
                return $result;
            }

            $sql = <<<sql
            WITH t AS (
              select "albumId", case {$tagCond} else null end as "tagId"
              from "albums"
              where "statusId" = 1
            )
            , v as (
              SELECT "tagId", array_agg( "albumId" order by "albumId" desc ) as "ids", count(*)
              FROM t
              WHERE "tagId" IS NOT NULL
              GROUP BY 1
            )
            SELECT "tagId", "ids"[0:11], "count" FROM v
sql;

            $cmd = new SqlCommand( $sql, $conn );
            $ds  = $cmd->Execute();
            while ( $ds->Next() ) {
                $a           = new AlbumByTag();
                $a->AlbumIds = $ds->GetComplexType( 'ids', 'int[]' );
                $a->TagId    = $ds->GetInteger( 'tagId' );
                $a->Tag      = $tags[$a->TagId];
                $a->Count    = $ds->GetInteger( 'count' );

                $result[$a->TagId] = $a;
            }

            return $result;
        }


        /**
         * Get All Parent & Child Tags for specific parentTagId
         * @param Tag[] $map
         * @param int   $tagId main tag id
         * @return Tag[]
         */
        public static function FilterTags( $map, $tagId ) {
            $result = [ ];

            foreach ( $map as $t ) {
                if ( $t->path && in_array( $tagId, $t->path, true ) ) {
                    $result[$t->tagId] = $t->tagId;
                }
            }

            return $result;
        }
    }