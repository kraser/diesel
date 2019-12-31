<?php
class GolosInstaller extends Installer
{
    public function __construct ()
    {
        $this->parentClass = "Golos";
        $this->path = __DIR__.DS;
    }
}
