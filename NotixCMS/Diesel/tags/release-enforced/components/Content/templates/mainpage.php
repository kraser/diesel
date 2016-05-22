<?php
echo "<h1>$tagH1</h1>";
echo Starter::app ()->getModule("Slider")->Run();
echo __FILE__."<br>";
echo Logo::render();