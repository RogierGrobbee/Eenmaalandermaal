<?php
require('partial files\header.php');

require('partial files\models\rubriek.php');
$rubriekArray = loadAllRubrieken();?>

    <h1>Feedback op </h1>

<?php require('partial files\sidebar.php');
loadRubriekenSidebar(null); ?>

    <textarea name="commentaar" cols="40" rows="5" style="min-width: 300px; min-height: 200px;"></textarea>

<?php require('partial files\footer.php')?>