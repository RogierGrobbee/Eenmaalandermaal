<?php
/*TESSTTTT
test van rogier
test van jasper
Test van rogier2
test van tim2

hoihoihohiohiohioio mooie test merge ding*/

class Testtt{
    public $variabel;

    function __construct() {
        $this->variabel = "wat<br>";
        echo $this->variabel;
    }

    function printen(){
        echo $this->variabel;
    }
}

$test = new Testtt();
$test->variabel = "hoi";
$test->printen();
?>
