<?php

class OfficeInstaller extends Installer
{

    public function __construct ()
    {
        $this->parentClass = "PrivateOffice";
        $this->path = __DIR__ . DS;
    }

    public function Run ()
    {/*
      $classes = array("User");
      foreach($classes as $class)
      {
      $this->parenClass = $class;
      parent::Run();
      }
     */
    }
}
