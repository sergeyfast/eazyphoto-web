<?php
    $host = !empty( $argv[1] ) ? $argv[1] : null;
    $url  = !empty( $argv[2] ) ? $argv[2] : null;

    if ( !empty( $host ) && !empty( $url ) ) {
        $queryString  = parse_url( $url, PHP_URL_QUERY );
        $serverParams = array (
            'REDIRECT_STATUS'   => 200
            , 'HTTP_HOST'       => $host
            , 'SERVER_NAME'     => $host
            , 'SERVER_PORT'     => '80'
            , 'SCRIPT_FILENAME' => dirname( __FILE__ ) . '/eaze.php'
            , 'SERVER_PROTOCOL' => 'HTTP/1.1'
            , 'REQUEST_METHOD'  => 'GET'
            , 'QUERY_STRING'    => $queryString
            , 'REQUEST_URI'     => $url
            , 'SCRIPT_NAME'     => '/eaze.php'
            , 'PHP_SELF'        => '/eaze.php'
        );

        if ( !empty( $queryString ) ) {
            parse_str( $queryString, $_GET );
            parse_str( $queryString, $_POST );
            parse_str( $queryString, $_REQUEST );
        }

        foreach ($serverParams as $k => $v) {
            $_SERVER[$k] = $v;
        }

        include 'eaze.php';
    } else {
        die ( 'Usage: ' . basename( $_SERVER['PHP_SELF'] ) . " host url \n" );
    }
?>