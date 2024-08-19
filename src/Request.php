<?php



namespace Solenoid\RPC;



use \Solenoid\HTTP\Server;
use \Solenoid\HTTP\Request as HTTPRequest;
use \Solenoid\HTTP\Response;
use \Solenoid\HTTP\Status;



class Request
{
    private static self        $instance;
    private static HTTPRequest $request;



    public bool       $valid;

    public string     $subject;
    public string     $verb;
    public ?\stdClass $input;



    # Returns [stdClass|null]
    private static function parse_input (string $body, string $content_type)
    {
        if ( $body === '' ) return null;



        // (Setting the value)
        $value = null;

        switch ( $content_type )
        {
            case 'application/json':
                // (Getting the value)
                $value = json_decode( $body );
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
                    $headers = explode( "\r\n\r\n", $part );
                    $body    = array_pop( $headers );



                    // (Setting the values)
                    $name     = null;
                    $filename = null;



                    foreach ( $headers as $header )
                    {// Processing each entry
                        // (Getting the values)
                        [ $k, $v ] = explode( $header, ': ', 2 );

                        if ( $k === 'Content-Disposition' )
                        {// Match OK
                            // (Getting the value)
                            $p = explode( '; ', $v );

                            if ( $p[0] === 'form-data' )
                            {// Match OK
                                // (Getting the value)
                                $pp = explode( '=', $p[1] );

                                if ( $pp[0] === 'name' )
                                {// Match OK
                                    // (Getting the value)
                                    $name = trim( $pp[1], " \n\r\t\v\0\"" );



                                    if ( isset( $p[2] ) )
                                    {// Value found
                                        // (Getting the value)
                                        $ppp = explode( '=', $p[2] );

                                        if ( $ppp[0] === 'filename' )
                                        {// Match OK
                                            // (Getting the value)
                                            $filename = trim( $ppp[1], " \n\r\t\v\0\"" );
                                        }
                                    }



                                    if ( $name )
                                    {// Value found
                                        // (Getting the value)
                                        $v = [];



                                        // (Setting the value)
                                        $v = new \stdClass();

                                        $v->headers = $headers;
                                        $v->body    = $body;



                                        if ( $filename )
                                        {// Value found
                                            // (Appending the value)
                                            $v->filename = $filename;



                                            // (Appending the value)
                                            $value[ $name ][] = $v;
                                        }
                                        else
                                        {// Value not found
                                            // (Getting the value)
                                            $value[ $name ] = $v;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }



                // (Setting the value)
                $v = new \stdClass();

                foreach ( $value as $k => $v )
                {// Processing each entry
                    // (Getting the value)
                    $v->{ $k } = $v;
                }



                // (Getting the value)
                $value = $v;
            break;
        }



        // Returning the value
        return $value;
    }



    # Returns [self]
    private function __construct ()
    {
        // (Getting the value)
        self::$request = HTTPRequest::fetch();



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
        $input = self::parse_input( self::$request->body, $content_type );



        // (Getting the value)
        $action_parts = explode( '::', $action );

        if ( count( $action_parts ) === 1 )
        {// (There is only the verb)
            // (Getting the values)
            $this->subject = '';
            $this->verb    = $action_parts[0];
        }
        else
        {// (There are subject and verb)
            // (Getting the values)
            $this->subject = $action_parts[0];
            $this->verb    = $action_parts[1];
        }



        // (Getting the value)
        $this->input = $input;



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