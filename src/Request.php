<?php



namespace Solenoid\RPC;



use \Solenoid\HTTP\Server;
use \Solenoid\HTTP\Response;
use \Solenoid\HTTP\Status;



class Request extends \Solenoid\HTTP\Request
{
    private static self $instance;



    public static bool      $valid;

    public static string    $subject;
    public static string    $verb;
    public static \stdClass $input;



    # Returns [self]
    private function __construct ()
    {
        // (Calling the function)
        parent::fetch();



        if ( $this->method !== 'RPC' )
        {// Match failed
            // (Setting the value)
            self::$valid = false;



            // Returning the value
            return
                Server::send( new Response( new Status(400), [], [ 'error' => [ 'message' => 'RPC :: Method is not valid' ] ]) )
            ;
        }



        // (Getting the value)
        $action = $this->headers['Action'];

        if ( !isset( $action ) )
        {// Value not found
            // (Setting the value)
            self::$valid = false;



            // Returning the value
            return
                Server::send( new Response( new Status(400), [], [ 'error' => [ 'message' => 'RPC :: Action is required' ] ]) )
            ;
        }



        // (Getting the value)
        $input = ( $this->body === '' ) ? [] : json_decode( $this->body );

        if ( $input === null )
        {// (Unable to decode the body as JSON)
            // (Setting the value)
            self::$valid = false;



            // Returning the value
            return
                Server::send( new Response( new Status(400), [], [ 'error' => [ 'message' => 'RPC :: Request-Body format is not valid' ] ]) )
            ;
        }



        // (Getting the value)
        $action_parts = explode( '::', $action );

        if ( count( $action_parts ) === 1 )
        {// (There is only the verb)
            // (Getting the values)
            self::$subject = '';
            self::$verb    = $action_parts[0];
        }
        else
        {// (There are subject and verb)
            // (Getting the values)
            self::$subject = $action_parts[0];
            self::$verb    = $action_parts[1];
        }



        // (Getting the value)
        self::$input = $input;



        // (Setting the value)
        self::$valid = true;
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