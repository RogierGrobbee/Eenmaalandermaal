<?php
require('partial files\models\voorwerp.php');
require('partial files\models\feedback.php');
require('partial files\models\miscellaneous.php');

$voorwerp = "";

if(!isset($_GET['voorwerpnummer'])){
    header('Location: index.php');
}
else{
    $voorwerp = getVoorwerpById($_GET['voorwerpnummer']);
}

session_start();
if(isset($_SESSION['return'])){
    unset($_SESSION['return']);
}

if(!isset($_SESSION['user'])){
    $_SESSION['message'] = "Log eerst in voordat u feedback achterlaat.";
    $_SESSION['return'] = $_GET['voorwerpnummer'];
    header('Location: login.php');
}
else{
    if($voorwerp->koper != $_SESSION['user']){
        header('Location: index.php');
    }
    else{
        $feedbackgegeven = isFeedbackGiven($voorwerp->voorwerpnummer);

        if(!empty($feedbackgegeven)){
            if($feedbackgegeven->gebruikersnaam == $_SESSION['user']){
                $_SESSION['message'] = "U heeft al feedback gegeven aan deze gebruiker.";
                header('Location: profiel/overzicht.php?user='.$voorwerp->verkoper);
            }
        }
    }
}

if(isset($_POST['beoordeling']) && isset($_POST['commentaar'])){
    if(insertFeedbackKoper($voorwerp->voorwerpnummer, $_SESSION['user'], strip_html_tags($_POST['beoordeling']),
        strip_html_tags($_POST['commentaar']))){
        $_SESSION['message'] = "Bedankt voor het geven van feedback";
        header('Location: profiel/overzicht.php?user='.$voorwerp->verkoper);
    }
}

require('partial files\header.php');
require('partial files\models\rubriek.php');
$rubriekArray = loadAllRubrieken();?>

    <h1>Feedback op </h1>

<?php require('partial files\sidebar.php');
loadRubriekenSidebar(null); ?>

    <?php
        if (!empty($errorMessage)) {
            echo "<div class='alert alert-danger error'>$errorMessage</div>";
        }


    echo '<p class="feedback-description" ">U heeft <a href="veiling.php?voorwerpnummer='. $voorwerp->voorwerpnummer .'">'.
        $voorwerp->titel .'</a> gewonnen<br>Geef feedback op <a href="profiel/overzicht.php?user='. $voorwerp->verkoper .'">'.
        $voorwerp->verkoper.'</a></p>';


    echo "<form action='feedback.php?voorwerpnummer=" . $voorwerp->voorwerpnummer . "'" . "method='post'>"
    ?>
        <h2>Beoordeling</h2>
        <label class="radio-rating">
            <input type="radio" name="beoordeling" value="positief" required/>
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
        <textarea name="commentaar" class="feedback-comment" maxlength="75" required></textarea><Br><br>

        <button type="submit">Verzend</button>
    </form>

<?php require('partial files\footer.php')?>