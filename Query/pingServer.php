<?php
/**
 * Created by PhpStorm.
 * User: Samer
 * Date: 2015-06-29
 * Time: 10:55 AM
 */
Header( 'Content-Type: text/plain' );
Header( 'X-Content-Type-Options: nosniff' );


if (isset($_GET['host']) && isset($_GET['port'])) {
    $serverHost = $_GET['host'];
    $serverPort = $_GET['port'];
    if (isset($_GET['steam'])){
        require '../Query/SourceQuery/SourceQuery.class.php';
        // For the sake of this example
        // Edit this ->
        define( 'SQ_SERVER_ADDR', $serverHost );
        define( 'SQ_SERVER_PORT', $serverPort );
        define( 'SQ_TIMEOUT',     1 );
        define( 'SQ_ENGINE',      SourceQuery :: SOURCE );
        // Edit this <-

        $Query = new SourceQuery( );

        try
        {
            $Query->Connect( SQ_SERVER_ADDR, SQ_SERVER_PORT, SQ_TIMEOUT, SQ_ENGINE );

            print_r( $Query->GetInfo( ) );
        }
        catch( Exception $e )
        {
            echo $e->getMessage( );
        }

        $Query->Disconnect( );
    }


}
else
    print ('Failed to retrieve host and port.');