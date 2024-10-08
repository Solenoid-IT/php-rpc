<?php



namespace Solenoid\RPC;



use \Solenoid\HTTP\Server;
use \Solenoid\HTTP\Request as HttpRequest;
use \Solenoid\HTTP\Response;
use \Solenoid\HTTP\Status;

use \Solenoid\RPC\FormData\Part;



class Request
{
    private static self        $instance;
    private static HttpRequest $request;



    public bool   $valid;

    public string $subject;
    public string $verb;



    # Returns [self]
    private function __construct ()
    {
        // (Getting the value)
        self::$request = HttpRequest::fetch();



        if ( self::$request->method !== 'RPC' )
        {// Match failed
            // (Setting the value)
            $this->valid = false;



            // Returning the value
            return
                Server::send( new Response( new Status(400), [], [ 'error' => [ 'message' => 'RPC :: Method is not valid' ] ]) )
            ;
        }



        // (Getting the value)
        $action = self::$request->headers['Action'];

        if ( !isset( $action ) )
        {// Value not found
            // (Setting the value)
            $this->valid = false;



            // Returning the value
            return
                Server::send( new Response( new Status(400), [], [ 'error' => [ 'message' => 'RPC :: Action is required' ] ]) )
            ;
        }



        // (Getting the value)
        $content_type = self::$request->headers['Content-Type'];

        if ( !isset( $content_type ) )
        {// Value not found
            // (Setting the value)
            $this->valid = false;



            // Returning the value
            return
                Server::send( new Response( new Status(400), [], [ 'error' => [ 'message' => 'RPC :: Content-Type is required' ] ]) )
            ;
        }



        // (Getting the value)
        $action_parts = explode( '::', $action );

        if ( count( $action_parts ) === 1 )
        {// (There is only the verb)
            // (Getting the values)
            $this->subject = '';
            $this->verb    = $action;
        }
        else
        {// (There are subject and verb)
            // (Getting the values)
            $this->subject = $action_parts[0];
            $this->verb    = $action_parts[1];
        }



        // (Setting the value)
        $this->valid = true;
    }



    # Returns [self]
    public static function fetch ()
    {
        if ( !isset( self::$instance ) )
        {// Value not found
            // (Getting the value)
            self::$instance = new self();
        }



        // Returning the value
        return self::$instance;
    }



    # Returns [bool]
    public function verify (string $token)
    {
        // (Getting the value)
        $auth_token = self::$request->headers['Auth-Token'];

        if ( !isset( $auth_token ) )
        {// Match failed
            // (Sending the response)
            Server::send( new Response( new Status(400), [], [ 'error' => [ 'message' => 'RPC :: Token is required' ] ]) );



            // Returning the value
            return false;
        }



        if ( !hash_equals( $token, $auth_token ) )
        {// Match failed
            // (Sending the response)
            Server::send( new Response( new Status(401), [], [ 'error' => [ 'message' => 'RPC :: Client is not authorized' ] ] ) );



            // Returning the value
            return false;
        }



        // Returning the value
        return true;
    }



    # Returns [mixed]
    public function parse_body ()
    {
        // (Getting the value)
        $body = &self::$request->body;

        if ( $body === '' ) return $body;



        // (Setting the value)
        $value = '';

        switch ( self::$request->headers['Content-Type'] )
        {
            case 'application/json':
                // (Getting the value)
                $value = json_decode( $body, true );
            break;

            case 'multipart/form-data':
                // (Setting the value)
                $value = [];



                // (Getting the value)
                $delimiter = substr( $body, 0, strpos( $body, "\r\n" ) );



                // (Getting the value)
                $parts = explode( "$delimiter\r\n", $body );

                for ( $i = 1; $i < count( $parts ); $i++ )
                {// Iterating each index
                    // (Getting the value)
                    $part = $parts[$i];

                    // (Getting the values)
                    [ $headers, $body ] = explode( "\r\n\r\n", $part );
                    $headers            = explode( "\r\n", $headers );



                    // (Getting the value)
                    $part = new Part( $headers, $body );



                    // (Getting the value)
                    $part_name = $part->get_name();

                    if ( $part_name )
                    {// Value found
                        // (Appending the value)
                        $value[ $part_name ][] = new Part( $headers, $body );
                    }
                }



                // (Setting the value)
                $val = [];

                foreach ( $value as $k => $v )
                {// Processing each entry
                    // (Getting the value)
                    $val[$k] = count($v) === 1 ? $v[0] : $v;
                }



                // (Getting the value)
                $value = $val;
            break;
        }



        // Returning the value
        return $value;
    }



    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return json_encode( get_object_vars( $this ) );
    }



    /*

    # Returns [void]
    public function __destruct ()
    {
        if ( Server::$response )
        {// (Server has already sent a response to the client)
            // Returning the value
            return;
        }



        if ( self::$valid && !Server::$response )
        {// (Request is valid but there is no response sent from this action)
            // Returning the value
            return
                Server::send( Response::create( 500, [], [ 'error' => [ 'message' => 'RPC :: Response has not been sent' ] ] ) )
            ;
        }



        // Returning the value
        return
            Server::send( Response::create( 404, [], [ 'error' => [ 'message' => 'RPC :: Action not found' ] ] ) )
        ;
    }

    */
}



?>