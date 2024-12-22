<?php



namespace Solenoid\RPC;



use \Solenoid\HTTP\Server;
use \Solenoid\HTTP\Request;
use \Solenoid\HTTP\Response;
use \Solenoid\HTTP\Status;



class Action
{
    public static string $class;
    public static string $method;



    # Returns [void]
    public static function run (string $ns_prefix)
    {
        // (Getting the value)
        $request = Request::fetch();



        // (Getting the value)
        $action = $request->headers['Action'];

        if ( !$action )
        {// Value not found
            // Returning the value
            return
                Server::send( new Response( new Status(400), [], [ 'error' => [ 'message' => 'RPC :: Action is required' ] ] ) )
            ;
        }



        // (Getting the values)
        [ $class, $method ] = explode( '.', $action, 2 );

        if ( !$method )
        {// Value not found
            // Returning the value
            return
                Server::send( new Response( new Status(400), [], [ 'error' => [ 'message' => 'RPC :: Action-Method is required' ] ] ) )
            ;
        }



        // (Getting the value)
        self::$class = $class;



        // (Getting the value)
        $class = $ns_prefix . str_replace( '/', '\\', $class );

        if ( !class_exists( $class ) )
        {// (Class not found)
            // Returning the value
            return
                Server::send( new Response( new Status(400), [], [ 'error' => [ 'message' => 'RPC :: Action-Class not found' ] ] ) )
            ;
        }



        if ( !method_exists( $class, $method ) )
        {// (Method not found)
            // Returning the value
            return
                Server::send( new Response( new Status(400), [], [ 'error' => [ 'message' => 'RPC :: Action-Method not found' ] ] ) )
            ;
        }



        // (Getting the value)
        self::$method = $method;



        // (Getting the value)
        $fn = "$class::$method";



        // (Calling the function)
        $response = call_user_func_array( $fn, [] );



        // Returning the value
        return $response;
    }
}



?>