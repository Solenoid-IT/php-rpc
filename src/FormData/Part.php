<?php



namespace Solenoid\RPC\FormData;



class Part
{
    public array  $headers;
    public string $body;



    # Returns [self]
    public function __construct (array $headers = [], string $body = '')
    {
        // (Getting the value)
        $this->headers = $headers;
        $this->body    = $body;
    }



    # Returns [string|false]
    public function get (string $key)
    {
        foreach ( $this->headers as $header )
        {// Processing each entry
            // (Getting the values)
            [ $k, $v ] = explode( ': ', $header, 2 );

            if ( strtolower( $k ) === strtolower( $key ) )
            {// Match OK
                // Returning the value
                return $v;
            }
        }
    }

    # Returns [array<string>]
    public function get_all (string $key)
    {
        // (Setting the value)
        $results = [];

        foreach ( $this->headers as $header )
        {// Processing each entry
            // (Getting the values)
            [ $k, $v ] = explode( ': ', $header, 2 );

            if ( strtolower( $k ) === strtolower( $key ) )
            {// Match OK
                // (Appending the value)
                $results[] = $v;
            }
        }



        // Returning the value
        return $results;
    }



    # Returns [string|false]
    public function get_name ()
    {
        // (Getting the value)
        $value = $this->get( 'Content-Disposition' );

        if ( $value === false )
        {// Value not found
            // Returning the value
            return false;
        }



        if ( preg_match( '/form-data; name="([^\"]+)"/', $value, $matches ) === 1 )
        {// Match OK
            // Returning the value
            return $matches[1];
        }



        // Returning the value
        return false;
    }

    # Returns [string|false]
    public function get_filename ()
    {
        // (Getting the value)
        $value = $this->get( 'Content-Disposition' );

        if ( $value === false )
        {// Value not found
            // Returning the value
            return false;
        }



        if ( preg_match( '/form-data; name="([^\"]+)"; filename="([^\"]+)"/', $value, $matches ) === 1 )
        {// Match OK
            // Returning the value
            return $matches[2];
        }



        // Returning the value
        return false;
    }



    # Returns [string]
    public function __toString ()
    {
        // Returning the value
        return implode( "\r\n", $this->headers ) . "\r\n\r\n$this->body";
    }
}



?>