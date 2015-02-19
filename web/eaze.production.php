<?php
    use Eaze\Core\Logger;
    use Eaze\Core\Package;
    use Eaze\Core\Request;
    use Eaze\Site\SiteManager;

    define( 'WITH_PACKAGE_COMPILE', true );

    // Initialize Logger
    include_once 'lib.eaze/Eaze.Core/Logger.php';
    Logger::Init( ELOG_DEBUG  );
    Logger::Init( ELOG_WARNING );

    include_once 'lib.eaze/Eaze.Core/Package.php' ;
    Package::LoadClasses( 'Eaze\Core\Convert', 'Eaze\Core\DateTimeWrapper', 'Eaze\Model\IFactory', 'User', 'ITreeFactory' );

    mb_internal_encoding("UTF-8");
    mb_http_output("UTF-8");

    if ( defined( 'WITH_PACKAGE_COMPILE' ) && WITH_PACKAGE_COMPILE ) Logger::Info( 'With package compiled' );
    if ( isset( $_POST[session_name()] ) ) {
        session_id( $_POST[session_name()] ); // uploadify hack
    }

    Request::Init();
    $__level = Request::getParameter( '__level' );
    if ( !is_null( $__level ) ) {
        Logger::LogLevel( $__level );
    }

    SiteManager::DetectSite();
    Logger::Info( 'Done' );
