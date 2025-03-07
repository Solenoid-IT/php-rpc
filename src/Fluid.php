<?php



namespace Solenoid\RPC;



interface Fluid
{
    # Returns [void]
    public static function find ();
    public static function list ();
    public static function update ();
    public static function insert ();
    public static function delete ();
}



?>