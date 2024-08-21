<?php



namespace Solenoid\RPC\FormData;



class FormData
{
    // (Setting the value)
    public array $content;



    # Returns [self]
    public function __construct ()
    {
        // (Setting the value)
        $this->content = [];
    }



    # Returns [self]
    public function append (string $name, string $value, ?string $filename = null)
    {
        // (Setting the value)
        $headers = [];

        // (Appending the value)
        $headers[] = "Content-Disposition: form-data; name=\"$name\"" . ( $filename ? "; filename=\"$filename\"" : '' );

        if ( $filename )
        {// Value found
            // (Doing nothing)
        }



        // (Setting the value)
        $part = new \stdClass();

        // (Getting the values)
        $part->headers = $headers;
        $part->body    = $value;



        // (Appending the value)
        $this->content[ $name ][] = $part;



        // Returning the value
        return $this;
    }



    # Returns [string|false]
    public function get (string $name)
    {
        foreach ( $this->content as $k => $v )
        {// Processing each entry
            if ( $k === $name )
            {// Match OK
                // Returning the value
                return $v;
            }
        }



        // Returning the value
        return false;
    }

    # Returns [array<string>]
    public function get_all (string $name)
    {
        // (Setting the value)
        $results = [];

        foreach ( $this->content as $k => $v )
        {// Processing each entry
            if ( $k === $name )
            {// Match OK
                // (Appending the value)
                $results[] = $v;
            }
        }



        // Returning the value
        return $results;
    }



    # Returns [self]
    public function set (string $name, string $value, ?string $filename = null)
    {
        foreach ( $this->content[ $name ] as $k => $v )
        {// Processing each entry
            // (Setting the value)
            $headers = [];

            // (Appending the value)
            $headers[] = "Content-Disposition: form-data; name=\"$name\"" . ( $filename ? "; filename=\"$filename\"" : '' );



            // (Getting the value)
            $nv = new \stdClass();

            // (Getting the values)
            $nv->headers = $headers;
            $nv->body    = $value;



            // (Getting the value)
            $this->content[ $name ][$k] = $nv;
        }



        // Returning the value
        return $this;
    }



    # Returns [self]
    public function delete (string $name)
    {
        // (Removing the element)
        unset( $this->content[ $name ] );



        // Returning the value
        return $this;
    }



    # Returns [array<string>]
    public function list_keys ()
    {
        // Returning the value
        return array_keys( $this->content );
    }

    # Returns [array<string>]
    public function list_values ()
    {
        // Returning the value
        return array_values( $this->content );
    }



    # Returns [string]
    public function __toString ()
    {
        // (Getting the value)
        $boundary = bin2hex( random_bytes( 30 / 2 ) );



        // (Setting the value)
        $value = '';

        foreach ( $this->content as $k => $v )
        {// Processing each entry
            // (Appending the values)
            $value .= str_pad( $boundary, 59, '-', STR_PAD_LEFT );
            $value .= "\r\n";
            
            foreach ( $v->headers as $header )
            {// Processing each entry
                // (Appending the value)
                $value .= "$header\r\n";
            }

            // (Appending the value)
            $value .= "\r\n$v->body";
        }



        // (Appending the values)
        $value .= str_pad( $boundary, 59, '-', STR_PAD_LEFT ) . '--';



        // Returning the value
        return $value;
    }
}



?>