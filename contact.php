<?php include_once('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();
include_once('partial files\header.php'); ?>

<h1>Contact</h1>

<?php include_once('partial files\sidebar.php');
loadSidebar($rubriekArray, null); ?>

        <div class="row">
            <div class="col-sm-3">
            <p class="contact">iConcepts<br>
                Ruitenberglaan 26<br>
                6826 CC Arnhem</p>

            <p class="contact">
                T (026) 369 19 11<br>
                F (026) 364 50 66<br>
                E <a href="mailto:arnoud.bers@han.nl">arnoud.bers@han.nl</a>
            </p> </div>

        <div class="col-sm-9 map"> <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2457.0763960744107!2d5.948973315596098!3d51.98726118332493!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47c7a46f8160614d%3A0x3a1bc587c5374eeb!2sRuitenberglaan+26%2C+6826+CC+Arnhem!5e0!3m2!1snl!2snl!4v1480685397247"
                width="100%" height="450px" frameborder="0"></iframe>
        </div>

<?php include_once('partial files\footer.php')?>