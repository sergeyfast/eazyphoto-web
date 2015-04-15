<?php
    use Eaze\Core\Convert;
    use Eaze\Core\Request;
    use Eaze\Database\ConnectionFactory;
    use Eaze\Database\SqlCommand;
    use Eaze\Helpers\ArrayHelper;
    use Eaze\Site\Site;

    /**
     *
     * um Search
     * @package    EazyPhoto
     * @subpackage Site
     * @author     sergeyfast
     */
    class AlbumSearch {

        const Tag = 'tag',
            Year = 'year',
            Story = 'story';

        /**
         * Fields
         * @var string[]
         */
        public static $Fields = [
            self::Year, self::Tag, self::Story
        ];

        /**
         * Sort Fields
         * @var string[]
         */
        public static $SortFields = [
            'event' => '"startDate"', 'created' => '"albumId"'
        ];

        /**
         * Distributions
         * @var array
         */
        public $Counts = [ ];

        /**
         * Is Story Mode
         * @var bool
         */
        public $IsStory;

        /**
         * Tag
         * @var Tag
         */
        public $Tag;

        /**
         * Year
         * @var int
         */
        public $Year;

        /**
         * @var string
         */
        public $TagAlias;

        /**
         * Sort
         * @var string
         */
        public $Sort;

        /**
         * Tag Map
         * @var Tag[]
         */
        public $TagMap = [ ];

        /**
         * Tag Aliases
         * @var string[]
         */
        public $TagAliases = [];

        /**
         * @var int[]
         */
        public $Years = [ ];


        /**
         * @var Tag[]
         */
        public $Tags = [ ];

        /**
         * @var User
         */
        private $user;

        /**
         * Create Tags
         */
        public function __construct() {
            $this->user   = AuthUtility::GetCurrentUser( 'User' );
            $this->TagMap = TagUtility::GetAllTags();
            foreach( $this->TagMap as $t ) {
                $this->TagAliases[$t->alias] = $t;
            }

            $this->InitYears();
        }


        /**
         * Get From Request
         * @return AlbumSearch
         */
        public static function GetFromRequest() {
            $as                = new AlbumSearch();
            $as->TagAlias      = Request::GetString( 'tag' );
            $as->Year        = Request::GetInteger( 'year' );
            $as->Sort        = Request::GetString( 'sort' );
            $as->IsStory     = Request::GetBoolean( 'story' );

            if ( $as->TagAlias ) {
                $as->Tag = ArrayHelper::GetValue( $as->TagAliases, $as->TagAlias );
                if ( !$as->Tag ) {
                    $as->TagAlias = null;
                }
            }

            if ( !$as->Sort || empty( self::$SortFields[$as->Sort] ) ) {
                $as->Sort = key( self::$SortFields );
            }

            // TODO year check
            if ( $as->Year && ( $as->Year < 1900 || $as->Year > date( 'Y' ) + 5 ) ) {
                $as->Year = null;
            }

            return $as;
        }


        /**
         * Get Search array
         * @return array
         */
        public function GetSearch() {
            return [
                'isPrivate'   => $this->user  ? null : false,
                'geStartDate' => $this->Year ? Convert::ToDate( '01.01.' . $this->Year ) : null,
                'leStartDate' => $this->Year ? Convert::ToDate( '31.12.' . $this->Year ) : null,
            ];
        }


        /**
         * Get Custom Sql for AlbumFactory
         * @return string custom sql
         */
        public function GetCustomSql() {
            $t = $this->Tag ? array_keys( TagUtility::FilterTags( $this->TagMap, $this->Tag->tagId ) ): [];
            return AlbumUtility::GetSearchCustomSql( $t, $this->IsStory );
        }


        /**
         * Get Sql Order By String
         * @return string
         */
        public function GetOrderBySql() {
            return sprintf( '%s %s NULLS LAST', self::$SortFields[$this->Sort], 'DESC' );
        }


        /**
         * Get Url for Paginator
         * @return string
         */
        public function GetPagesUrl() {
            $url    = LinkUtility::GetAlbumsUrl();
            $params = http_build_query( array_filter( [
                'year'     => $this->Year,
                'story'    => $this->IsStory,
                'tag'      => $this->TagAlias,
                'sort'     => $this->Sort,
            ] ) );

            return $params ? sprintf( '%s?%s&page=', $url, $params ) : sprintf( '%s?page=', $url );
        }


        /**
         * Get Url For Filters
         * @param bool $noYear
         * @param bool $noStory
         * @param bool $noTag
         * @return string
         */
        public function GetUrl( $noYear = false, $noStory = false, $noTag = false ) {
            $url    = LinkUtility::GetAlbumsUrl();
            $params = http_build_query( array_filter( [
                'year'     => $noYear ? null : $this->Year,
                'story'    => $noStory ? null : $this->IsStory,
                'tag'      => $noTag ? null : $this->TagAlias,
                'sort'     => $this->Sort,
            ] ) );

            return Site::GetWebPath( $params ? sprintf( '%s?%s', $url, $params ) : $url );
        }


        /**
         * Get Url for Values
         * @param int    $year
         * @param bool   $story
         * @param string $tagAlias
         * @return string
         */
        public function GetUrlV( $year = null, $story = null, $tagAlias = null ) {
            $url    = LinkUtility::GetAlbumsUrl();
            $params = http_build_query( array_filter( [
                'year'     => $year ?: $this->Year,
                'story'    => $story ?: $this->IsStory,
                'tag'      => $tagAlias ?: $this->TagAlias,
                'sort'     => $this->Sort
            ] ) );

            return Site::GetWebPath( $params ? sprintf( '%s?%s', $url, $params ) : $url );
        }


        /**
         * Get Sort Url
         * @return string ...?sort=
         */
        public function GetSortUrl() {
            $url    = LinkUtility::GetAlbumsUrl();
            $params = http_build_query( array_filter( [
                'year'     => $this->Year,
                'story'    => $this->IsStory,
                'tag'      => $this->TagAlias,
            ] ) );

            return Site::GetWebPath( $params ? sprintf( '%s?%s&', $url, $params ) : $url . '?' ) . 'sort=';
        }


        /**
         * Init Years Distribution
         */
        public function InitYears() {
            $cmd = new SqlCommand( 'select distinct extract( "year" from "startDate" ) from "albums" where "statusId" = 1 order by 1 desc ', ConnectionFactory::Get() );
            $ds  = $cmd->Execute();
            while( $ds->Next() ) {
                $y = $ds->GetInteger(0);
                $this->Years[$y] = $y;
            }
        }
    }