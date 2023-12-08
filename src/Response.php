<?php



namespace Solenoid\RPC;



class Response
{
    public int   $status_code;

    public array $headers;
    public array $data;



    # Returns [self]
    public function __construct
    (
        int   $status_code = 200,

        array $headers     = [],
        array $data        = []
    )
    {
        // (Getting the values)
        $this->status_code = $status_code;

        $this->headers     = $headers;
        $this->data        = $data;
    }

    # Returns [Response]
    public static function create
    (
        int   $status_code = 200,

        array $headers     = [],
        array $data        = []
    )
    {
        // Returning the value
        return
            new Response
            (
                $status_code,

                $headers,
                $data
            )
        ;
    }
}



?>