<?php
/**
 * Description of Console
 *
 * @author kraser
 */
class Console extends CmsApplication
{
    public function __construct ( $alias, $config )
    {
        parent::__construct ( $alias, $config );
        Starter::import ( "console.*" );
    }

    public function Run ()
    {
        $arguments = $_SERVER['argv'];
        unset ( $arguments[0] );
        $commandClass = array_shift ( $arguments );
        $rc = new ReflectionClass ( ucfirst ( $commandClass ) . "Command" );
        $command = $rc->newInstance ( ucfirst ( $commandClass ), $this, $arguments );
        $command->Run();
    }
}