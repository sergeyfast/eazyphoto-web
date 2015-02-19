<?php
    use Eaze\Core\Request;
    use Eaze\Database\ConnectionFactory;
    use Eaze\Model\BaseFactory;
    use Eaze\Site\Site;

    /**
     * Vfs Rpc Service
     * @package    Base
     * @subpackage VFS
     * @author     Sergeyfast
     */
    class VfsRpcService extends BaseJsonRpcServer {

        /**
         * Jail Root Folder Id
         * If not empty: all operations will be jailed into this root folder except favorites
         * @var int
         */
        protected $jailRootFolderId;


        /**
         * Get Folder with Sub Folders
         * @param int $rootFolderId default is 1
         * @return array [id, name, parentId, folders]
         * @throws Exception
         */
        public function GetFolder( $rootFolderId = VfsUtility::RootFolderId ) {
            if ( !$rootFolderId ) {
                throw new Exception( 'Empty folder', 404 );
            }

            // check for jailed
            $rootFolderId = VfsUtility::SetJailRoot( $rootFolderId, $this->jailRootFolderId );

            $folder           = new VfsFolder();
            $folder->objectId = $rootFolderId;
            $folder->folderId = $rootFolderId;
            $folders          = VfsFolderFactory::GetChildren( $folder, array(), array( OPTION_WITH_PARENT => true ) );

            // set root folder and children to object
            if ( !empty( $folders[$rootFolderId] ) ) {
                $folder = $folders[$rootFolderId];
                $folder->nodes = $folders;

                unset( $folder->nodes[$rootFolderId] );
            } else {
                throw new Exception( 'Folder not found', 404 );
            }

            return VfsObjectConverter::GetVfsFolder( $folder );
        }


        /**
         * Get Folder Branch
         * @param int $folderId
         * @return array folders
         * @throws Exception
         */
        public function GetFolderBranch( $folderId ) {
            if ( !$folderId ) {
                throw new Exception( 'Empty folder', 404 );
            }

            // check for jailed
            $folderId = VfsUtility::SetJailRoot( $folderId, $this->jailRootFolderId );

            $folder = VfsFolderFactory::GetById( $folderId );
            if ( !$folder ) {
                throw new Exception( 'Folder not found', 404 );
            }

            $result = VfsFolderFactory::GetBranch( $folder );

            // check for jailed
            VfsUtility::CutJailedRoot( $this->jailRootFolderId, $result );


            return array_values( array_map( 'VfsObjectConverter::GetVfsFolder', $result ) );
        }


        /**
         * Get Files From Folder
         * @param int    $folderId      root folder id
         * @param string $query         file name
         * @param string $sortField     createdAt, title or fileSize
         * @param bool   $isDescending asc = false, desc = true
         * @param int    $page         current page
         * @param int    $pageSize     page size
         * @throws Exception
         * @return array
         */
        public function GetFiles( $folderId, $query = null, $sortField = 'createdAt', $isDescending = true , $page = 0, $pageSize = 100 ) {
            if ( !$folderId ) {
                throw new Exception( 'Empty folder', 404 );
            }

            if ( !in_array( $sortField, array( 'createdAt', 'title', 'fileSize' ) ) ) {
                throw new Exception( 'Invalid Sort Field', 404 );
            }

            // check for jailed
            $folderId = VfsUtility::SetJailRoot( $folderId, $this->jailRootFolderId );

            $search  = [ 'folderId' => $folderId, 'title%' => $query, 'page' => $page, 'pageSize' => $pageSize ];
            $options = [ BaseFactory::OrderBy => [ [ 'name' => $sortField, 'sort' => $isDescending ? 'DESC' : 'ASC' ] ] ];
            $result  = VfsFileFactory::Get( $search, $options );

            return array_values( array_map( 'VfsObjectConverter::GetVfsFile', $result ) );
        }


        /**
         * Count Files
         * @param      $folderId
         * @param null $query
         * @return int
         * @throws Exception
         */
        public function CountFiles( $folderId, $query = null ) {
            if ( !$folderId ) {
                throw new Exception( 'Empty folder', 404 );
            }

            // check for jailed
            $folderId = VfsUtility::SetJailRoot( $folderId, $this->jailRootFolderId );

            $search  = array( 'folderId' => $folderId, 'title%' => $query, 'pageSize' => 1  );
            $result  = VfsFileFactory::Count( $search );

            return $result;
        }


        /**
         * Create File in VFS from Temp File
         * @param int    $folderId     destination folder id
         * @param string $name         file name in vfs
         * @param string $relativePath relative path (only filename in this context)
         * @param string $type         mime type
         * @return bool|int
         */
        public function CreateFile( $folderId, $name, $relativePath, $type = null ) {
            // check for jailed
            $folderId = VfsUtility::SetJailRoot( $folderId, $this->jailRootFolderId );
            $path     = Site::GetRealPath( VfsUtility::TempPath . $relativePath );

            return VfsUtility::CreateFile( $folderId, $name, $path, $type );
        }


        /**
         * Move Files
         * @param int[] $fileIds
         * @param int   $destinationFolderId
         * @throws Exception
         * @return array|bool
         */
        public function MoveFiles( $fileIds, $destinationFolderId ) {
            // check for jailed
            $destinationFolderId = VfsUtility::SetJailRoot( $destinationFolderId, $this->jailRootFolderId );

            if ( !$fileIds ) {
                throw new Exception( 'Empty file ids', 404 );
            }

            $fileIds = array_filter( array_map( '\Eaze\Core\Convert::ToInt', $fileIds ) );
            if ( !$fileIds ) {
                throw new Exception( 'Invalid file ids', 404 );
            }

            $folder = VfsFolderFactory::GetById( $destinationFolderId );
            if ( !$folder ) {
                throw new Exception( 'Invalid Folder', 404 );
            }

            $file = new VfsFile();
            $file->folderId = $destinationFolderId;

            return VfsFileFactory::UpdateByMask( $file, array( 'folderId' ), array( '_fileId' => $fileIds ) );
        }


        /**
         * Delete Files
         * @param $fileIds
         * @throws Exception
         * @return bool
         */
        public function DeleteFiles( $fileIds ) {
            if ( !$fileIds ) {
                throw new Exception( 'Empty file ids', 404 );
            }

            $fileIds = array_filter( array_map( '\Eaze\Core\Convert::ToInt', $fileIds ) );
            if ( !$fileIds ) {
                throw new Exception( 'Invalid file ids', 404 );
            }

            $file = new VfsFile();
            $file->statusId = 3;

            return VfsFileFactory::UpdateByMask( $file, array( 'statusId' ), array( '_fileId' => $fileIds ) );
        }


        /**
         * Rename File on Server
         * @param int $fileId
         * @param string $name new name with extension, a-z0-9_-.
         * @throws Exception
         * @return bool
         */
        public function SetFilePhysicalName( $fileId, $name ) {
            if ( !$fileId || !$name ) {
                throw new Exception( 'Invalid Params', 404 );
            }

            $file = VfsFileFactory::GetById( $fileId );
            if ( !$file ) {
                throw new Exception( 'Invalid File Id', 404 );
            }

            // check new filename
            if ( !preg_match( '/[a-z0-9_\.-]+/i', $name, $matches ) ) {
                throw new Exception( 'Invalid File Name', 404 );
            }

            $pathInfo    = pathinfo( $file->path );
            $newFilename = $pathInfo['dirname'] . '/' . $name;
            $oldFilename = $file->path;
            $rootDir     = Site::GetRealPath( VfsUtility::RootDir );

            if ( file_exists( $rootDir . $newFilename ) ) {
                throw new Exception( 'File Exists', 404 );
            }

            // begin transaction for db and rename;
            ConnectionFactory::BeginTransaction();
            $file->path = $newFilename;

            if ( !VfsFileFactory::Update( $file )  ) {
                ConnectionFactory::CommitTransaction( false );

                throw new Exception( 'Update db Failed', 404 );
            }

            if ( !rename( $rootDir . $oldFilename, $rootDir . $newFilename ) ) {
                ConnectionFactory::CommitTransaction( false );

                throw new Exception( 'Rename Failed', 404 );
            }

            return ConnectionFactory::CommitTransaction( true );
        }


        /**
         * Search Folder by File Id
         * @param int $fileId vfs file id
         * @throws Exception
         * @return array file
         */
        public function SearchFolderByFileId( $fileId ) {
            if ( !$fileId ) {
                throw new Exception( 'Empty filename', 404 );
            }

            $file = VfsFileFactory::GetById( $fileId );
            if ( !$file ) {
                throw new Exception( 'File not found', 404 );
            }

            return VfsObjectConverter::GetVfsFolder( $file->folder );
        }


        /**
         * Search Folder By Filename
         * @param string $filename filename    /shared/files... or http://
         * @throws Exception
         * @return array folder
         */
        public function SearchFolderByFile( $filename ) {
            if ( !$filename ) {
                throw new Exception( 'Empty filename', 404 );
            }

            $vfsPath = Site::TranslatePathTemplate( VfsUtility::RootDir ); // /shared/files/
            $pathPos = strpos( $filename, $vfsPath );
            if ( $pathPos === false ) {
                $filePath = $filename;
            } else {
                $filePath = substr( $filename, $pathPos + strlen( $vfsPath ) );
            }

            $file = VfsFileFactory::GetOne( array( 'path' => $filePath ) );
            if ( !$file ) {
                throw new Exception( 'Path not found', 404 );
            }

            return VfsObjectConverter::GetVfsFolder( $file->folder );
        }

        /**
         * Get Favorite Folders
         * @return array folders
         */
        public function GetFavorites() {
            $result = VfsFolderFactory::Get(  array( "isFavorite" => true ) );

            if ( $this->jailRootFolderId ) {
                $result = array();
            }

            return array_map( 'VfsObjectConverter::GetVfsFolder', $result );
        }


        /**
         * Manage Favorite Folders
         * @param int  $folderId
         * @param bool $isInFavorites true = add, false = remove
         * @throws Exception
         * @return bool
         */
        public function ManageFavorites( $folderId, $isInFavorites ) {
            if ( !$folderId || $folderId == VfsUtility::RootFolderId ) {
                throw new Exception( 'Empty folder', 404 );
            }

            if ( $this->jailRootFolderId ) {
                throw new Exception( 'Jailed Root', 500 );
            }

            $folder             = new VfsFolder();
            $folder->isFavorite = $isInFavorites;
            $result             = VfsFolderFactory::UpdateByMask( $folder, array( 'isFavorite' ), array( 'folderId' => $folderId ) );

            return $result;
        }


        /**
         * Create Folder
         * @param int    $rootFolderId root folder id
         * @param string $name         folder name
         * @return bool
         * @throws Exception
         */
        public function CreateFolder( $rootFolderId, $name ) {
            // check for jailed
            $rootFolderId = VfsUtility::SetJailRoot( $rootFolderId, $this->jailRootFolderId );

            return VfsUtility::CreateFolder( $rootFolderId, $name );
        }


        /**
         * Delete Folder
         * @param int $folderId
         * @return bool
         * @throws Exception
         */
        public function DeleteFolder( $folderId ) {
            if ( !$folderId || $folderId == VfsUtility::RootFolderId ) {
                throw new Exception( 'Empty Folder', 404 );
            }

            // check for jailed
            if( VfsUtility::SetJailRoot( $folderId, $this->jailRootFolderId ) == $this->jailRootFolderId ) {
                throw new Exception( 'Jailed Folder', 500 );
            }

            $folder = VfsFolderFactory::GetById( $folderId );
            if ( !$folder ) {
                throw new Exception( 'Invalid Folder', 404 );
            }

            return VfsFolderFactory::Delete( $folder );
        }


        /**
         * Move Folder
         * @param int $folderId
         * @param int $destinationFolderId
         * @return bool
         * @throws Exception
         */
        public function MoveFolder( $folderId, $destinationFolderId ) {
            if ( !$folderId || !$destinationFolderId || $folderId == VfsUtility::RootFolderId ) {
                throw new Exception( 'Invalid Params', 404 );
            }

            $folder    = VfsFolderFactory::GetById( $folderId );
            $newFolder = VfsFolderFactory::GetById( $destinationFolderId );
            if ( !$folder || !$destinationFolderId ) {
                throw new Exception( 'Invalid folders', 404 );
            }

            return VfsFolderFactory::Move( $folder, $newFolder );
        }


        /**
         * Rename Folder
         * @param int    $folderId
         * @param string $name  folder name
         * @throws Exception
         * @return bool
         */
        public function RenameFolder( $folderId, $name ) {
            if ( !$folderId ) {
                throw new Exception( 'Empty folder', 404 );
            }

            if ( !$name ) {
                throw new Exception( 'Empty name', 404 );
            }

            // check for jailed
            if( VfsUtility::SetJailRoot( $folderId, $this->jailRootFolderId ) == $this->jailRootFolderId ) {
                throw new Exception( 'Jailed Folder', 500 );
            }

            $folder        = new VfsFolder();
            $folder->title = $name;
            $result        = VfsFolderFactory::UpdateByMask( $folder, array( 'title' ), array( 'folderId' => $folderId ) );

            return $result;
        }


        /**
         * Get Information about File Upload Urls and Parameters
         * @return array
         */
        public function HelpUpload() {
            return array(
                'temp' => array(
                    'url'      => Site::GetWebPath( 'vt://vfs/manage/file/temp/' )
                    , 'params' => array( 'fileUpload' )
                ), 'queue'  => array(
                    'url'      => Site::GetWebPath( 'vt://vfs/manage/file/queue/' )
                    , 'params' => array( 'Filedata', 'folderId' )
                )
            );
        }


        /**
         * Set Settings and Handle RPC Requests
         */
        public function Execute() {
            $this->jailRootFolderId = Request::getInteger( 'vrs_JailRootFolderId' );

            parent::Execute();
        }
    }

?>