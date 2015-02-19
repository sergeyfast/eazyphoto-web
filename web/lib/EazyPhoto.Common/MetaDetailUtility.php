<?php

    use Eaze\Database\ConnectionFactory;
    use Eaze\Model\BaseFactory;
    use Eaze\Model\ObjectInfo;

    /**
     * MetaDetailUtility
     * @package    EazyPhoto
     * @subpackage Common
     * @author     Sergeyfast
     */
    class MetaDetailUtility {

        /**
         * Get By Object
         * @param mixed $object
         * @param bool  $createNewObject create new empty object if not found in db
         * @return MetaDetail (from db or new instance with id & class)
         */
        public static function GetByObject( $object, $createNewObject = true ) {
            $result = null;
            $oi     = ObjectInfo::Get( $object );
            if ( !$oi ) {
                if ( $createNewObject ) {
                    $result           = new MetaDetail();
                    $result->statusId = StatusUtility::Enabled;
                }

                return $result;
            }

            $result = MetaDetailFactory::GetOne( [ 'objectId' => $oi->Id, 'objectClass' => $oi->Class ], [ BaseFactory::WithoutDisabled => false ] );
            if ( !$result && $createNewObject ) {
                $result              = new MetaDetail();
                $result->objectId    = $oi->Id;
                $result->objectClass = $oi->Class;
                $result->statusId    = StatusUtility::Enabled;
            }

            return $result;
        }


        /**
         * Detect metaDetail from objectInfo, url
         * @param ObjectInfo $objectInfo
         * @param string     $url
         * @return \MetaDetail
         */
        public static function GetForContext( $objectInfo, $url ) {
            $conn         = ConnectionFactory::Get();
            $converter    = $conn->GetConverter();
            $conditions   = [ ];
            $conditions[] = sprintf( '"url" = %s', $converter->ToString( $url ) );

            if ( $objectInfo ) {
                $conditions[] = sprintf( '("objectId" = %s AND "objectClass" = %s)', $converter->ToInt( $objectInfo->Id ), $converter->ToString( $objectInfo->Class ) );
            }

            $sql = sprintf( ' AND ( %s ) ', implode( ' OR ', $conditions ) );

            return MetaDetailFactory::GetOne( [ 'pageSize' => 2, 'statusId' => 1 ], [ BaseFactory::CustomSql => $sql ] );
        }
    }
