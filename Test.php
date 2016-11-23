<?php
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
