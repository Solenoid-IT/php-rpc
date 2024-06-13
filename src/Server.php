<?php



namespace Solenoid\RPC;



class Server
{
    public static ?Response $response = null;



    # Returns [void]
    public static function send (Response $response)
    {
        if ( Server::$response )
        {// (Server has already sent a response to the client)
            // Returning the value
            return;
        }



        // (Setting the http status)
        http_response_code( $response->status_code );



        foreach ($response->headers as $k => $v)
        {// Processing each entry
            // (Setting the header)
            header("$k: $v");
        }



        // (Setting the header)
        header('Content-Type: application/json');



        // Printing the value
        echo $response->data === null ? '' : json_encode( $response->data );



        // (Setting the value)
        self::$response = &$response;
    }
}



?>