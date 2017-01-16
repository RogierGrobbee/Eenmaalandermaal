<?php
include_once('..\partial files\header.php');
include_once('..\partial files\models\gebruiker.php');
include_once('..\partial files\models\bod.php');
include_once('..\partial files\models\voorwerp.php');
include_once('..\partial files\models\bestand.php');
include_once('..\partial files\models\miscellaneous.php');
include_once('..\partial files\sidebar.php');

$username = "";
if (isset($_SESSION["user"])) {
    $username = $_SESSION["user"];
}

if ($username == ""){
    header('Location: ../login.php');
}

$biedingen = getBiedingenByUsername($username);
if ($biedingen == null) {
    ?>
    <h1>Profiel</h1>
    <?php loadProfileSidebar($username, 3) ?>
    <div class="col-sm-12">
        <h3>Bod historie</h3>
        Geen biedningen
    </div>

    <?php
} else {
        ?>
        <h1>Profiel</h1>
        <?php loadProfileSidebar($username, 3) ?>
        <div class="col-sm-12">
            <h3>Bod historie</h3>
            <?php echoBiedingen($username) ?>
        </div>

        <?php

}

include_once('..\partial files\footer.php');


function echoBiedingen($username)
{
    $biedingen = getBiedingenByUsername($username);
    foreach ($biedingen as $k => $bod) {
        $voorwerpBiedingen = getBiedingenByVoorwerpnummer($bod->voorwerpnummer);
        $overboden = false;

        if($voorwerpBiedingen[0]->bodbedrag != $bod->bodbedrag){
            $overboden = true;
        }
        $prijs = $bod->bodbedrag;
        if ($prijs < 1) {
            $prijs = "0" . $prijs;
        }
        $foto = loadBestandByVoorwerpnummer($bod->voorwerpnummer);
        echoVoorwerp($bod, $prijs, $foto, $overboden);
    }
}

function echoVoorwerp($voorwerp, $prijs, $image, $overboden)
{
    $beschrijving = $voorwerp->beschrijving;

    $beschrijving = strip_html_tags($beschrijving);

    if (strlen($beschrijving) > 300) {
        $beschrijving = substr($beschrijving, 0, 280) . "... <span>lees verder</span>";
    }

    if ($prijs < 1) {
        $prijs = "0" . $prijs;
    }

    echo '  <div class="veilingitem">
                    <a href="/veiling.php?voorwerpnummer=' . $voorwerp->voorwerpnummer . '">
                        <img src="../pics/' . $image . '" alt="veilingsfoto" onError="this.onerror=null;this.src=\'../itemImages/'. $image . '\'">
                        <h4>' . $voorwerp->titel . '</h4>
                        <p>' . $beschrijving . '</p>';
    if($overboden) {
        echo '<p class="prijs" style="color:red;">€' . $prijs . ' - U bent overboden</p>';
    }
    else{
        echo '<p class="prijs">€' . $prijs . '</p>';
    }
    echo '<div class="veiling-info">
                            '.date("d-m-Y H:m", strtotime($voorwerp->bodtijdstip)).'
                        </div>
                    </a>
                </div>';
}

function loadSidebar($username)
{

    echo '<div class="list-group col-sm-3">';
    echo "<ul class='topnav' id='myTopnav''>";
    echo "<li><strong><div class='list-group-item rubriekList' href='#'>Menu</div></strong></li>";

    echo '<li><a class="list-group-item" href=overzicht.php?user='.$username.'>Overzicht</a></li>';
    echo '<li><a class="list-group-item" href=overzicht.php#>Wachtwoord wijzigen</a></li>';
    echo '<li><a class="list-group-item active" href=overzicht.php?user='.$username.'>Bod historie</a></li>';
    echo '<li><a class="list-group-item" href=overzicht.php#>Verkoper worden</a></li>';

    echo "<li class='icon''>";
    echo "</li>";
    echo "</ul>";
    echo '</div><div class="col-sm-9">';
}
?>