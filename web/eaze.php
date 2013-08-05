<?php
    /* Don't Forget to turn on mod_rewrite!  */

    define( 'WITH_PACKAGE_COMPILE', false  );

    // Initialize Logger
    include_once 'lib/Eaze.Core/Logger.php';
    Logger::Init( ELOG_DEBUG  );
    Logger::Init( ELOG_WARNING );

    include_once 'lib/Eaze.Core/Package.php' ;
    Package::LoadClasses( 'Convert', 'DateTimeWrapper', 'IFactory', 'User' );

    mb_internal_encoding( 'utf-8' );
    mb_http_output( 'utf-8' );

    BaseTreeFactory::SetCurrentMode( TREEMODE_ADJ );

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
?>