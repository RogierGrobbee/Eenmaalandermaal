<?php
require('partial files\models\voorwerp.php');
require('partial files\header.php');

if(!isset($_GET['voorwerpnummer'])){
    header('Location: index.php');
}

if(!isset($_SESSION['user'])){
    $_SESSION['message'] = "Login";

    header('Location: index.php');
}

require('partial files\models\rubriek.php');
$rubriekArray = loadAllRubrieken();?>

    <h1>Feedback op </h1>

<?php require('partial files\sidebar.php');
loadRubriekenSidebar(null); ?>

    <p style="font-size: 18px;">U heeft <a href="">product</a> gewonnen/geveild<br>
    Geef feedback op <a href="">gebruiker</a></p>

    <h2>Beoordeling</h2>
    <label class="radio-rating">
        <input type="radio" name="beoordeling" value="positief" />
        <img src="images/positief.png">
    </label>
    <label class="radio-rating">
        <input type="radio" name="beoordeling" value="neutraal" />
        <img src="images/neutraal.png">
    </label>
    <label class="radio-rating">
        <input type="radio" name="beoordeling" value="negatief" />
        <img src="images/negatief.png">
    </label>

    <h2>Commentaar</h2>
    <textarea name="commentaar" class="feedback-comment" maxlength="75"></textarea><Br><br>

    <button>Verzend</button>

<?php require('partial files\footer.php')?>