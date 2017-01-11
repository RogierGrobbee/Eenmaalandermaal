<?php
include_once('..\partial files\header.php');
include_once('..\partial files\models\gebruiker.php');
include_once('..\partial files\models\voorwerp.php');
include_once('..\partial files\models\gebruikerstelefoon.php');
include_once('..\partial files\sidebar.php');
include_once('..\partial files\models\bestand.php');
include_once('..\partial files\models\bod.php');
include_once('..\partial files\models\miscellaneous.php');

$username = "";
if (isset($_GET["user"])) {
    $username = $_GET["user"];
} else {
    if (isset($_SESSION["user"])) {
        $username = $_SESSION["user"];
    }
}

$loggedIn = false;
if (isset($_SESSION["user"])) {
    $sessionUsername = $_SESSION["user"];
    if ($username == $sessionUsername) {
        $loggedIn = true;
    }
}

if ($username == ""){
    header('Location: ../login.php');
}


$user = getUserByUsername($username);
if ($user == null) {
    echo '<h1>Profiel van ' . $username . '</h1>
    <div class="col-sm-12">
    Geen gebruiker gevonden
    </div>';
} else {
if ($loggedIn) {
    ?>
    <h1>Profiel</h1>
    <?php loadProfileSidebar($username, 1) ?>
    <div class="col-sm-12">
        <h3>Overzicht</h3>
        <div class="well">
            Username: <?php echo $user->gebruikersnaam; ?>
            <br>Naam: <?php echo $user->voornaam . " " . $user->achternaam ?>
            <br>Email: <?php echo $user->email ?>
            <br> <?php echoPhoneNumbers()
            ?>
        </div>
        <h3>Adres</h3>
        <div class="well">
            Adres: <?php echo $user->adresregel1; ?>
            <br>Postcode: <?php echo $user->postcode; ?>
            <br>Plaats: <?php echo $user->plaatsnaam; ?>
            <br>Land: <?php echo $user->land; ?>
        </div>
    </div>

    <?php
} else { ?>

<h1>Profiel van <?php echo $username ?></h1>
<div class="col-sm-12">
    <h3>Overzicht</h3>
    <div class="well">
        Username: <?php echo $user->gebruikersnaam; ?>
        <br>Naam: <?php echo $user->voornaam . " " . $user->achternaam ?>
        <br>Email: <?php echo $user->email ?>
        <br> <?php //echoPhoneNumbers($user)
        ?>
    </div>
    <h3>Beoordeling</h3>
    <div class="well">
        Hier komt wat stuff over de rating
    </div>
    <h3>Veilingen</h3>
        <?php echoVeilingen($username) ?>


    <?php
    }
    }


    include_once('..\partial files\footer.php');

    function echoPhoneNumbers()
    {
        echo '<br>';
        global $username;

        $phoneNumbersObjects = getPhoneNumbers($username);
        if (isset($_POST['remove'])) {
            foreach ($phoneNumbersObjects as $k => $number) {
                if ($number->telefoon == $_POST['remove']) {
                    unset($phoneNumbersObjects[$k]);
                    removePhoneNumber($username, $_POST['remove']);
                }
            }
        }
        $phoneNumbersObjects = getPhoneNumbers($username);
        $phoneNumbers = array();
        echo 'Telefoonnummers:';
        if (count($phoneNumbersObjects) < 2 && !isset($_POST['telefoon'])) {
            echo '<div class="row"><p class="telephone">' . $phoneNumbersObjects[0]->telefoon . '</p></div>';
        } else {
            foreach ($phoneNumbersObjects as $k => $number) {
                echo '<div class="row"><p class="telephone">' . $number->telefoon . '</p> <form method="post" style="float:left" ><button style="float:left; border-radius: 50px; " type="submit" name="remove" value="' . $number->telefoon . '"><b>X</b></button></form></div>';
                array_push($phoneNumbers, $number->telefoon);
            }
            if (isset($_POST['telefoon'])) {
                if (is_numeric($_POST['telefoon'])) {
                    if (!in_array($_POST['telefoon'], $phoneNumbers)) {
                        $temp = $phoneNumbersObjects[count($phoneNumbersObjects) - 1];
                        $hoogsteVolgnr = $temp->volgnr;
                        $hoogsteVolgnr++;

                        insertPhoneNumber($hoogsteVolgnr, $username, $_POST['telefoon']);
                        echo '<div class="row"><p class="telephone">' . $_POST['telefoon'] . '</p><form method="post" style="float: left"><button style="float: left; border-radius: 50px;" type="submit" name="remove" value="' . $_POST['telefoon'] . '"><b>X</b></button> </form></div>';
                    }
                }
            }
        }


        ?>
        <form method="post">
            <br>
            <div class="row">
                <div class="col-lg-3 col-md-5 col-sm-7 col-xs-12 add-number">
                    <input class="input-add-number" maxlength="10" value="" type="text" name="telefoon">
                </div>
                <div class="col-lg-3 col-md-4 col-sm-5 col-xs-12 add-number">
                    <button class="btn-add-number" type="submit" name="Toevoegen" value="toevoegen">Nummer toevoegen
                    </button>
                </div>
            </div>
        </form>
        <?php
    }

    function echoBiedingen($username)
    {
        $biedingen = getBiedingenByUsername($username);
        foreach ($biedingen as $k => $bod) {
            echo '<div class="well">';
            echo '<a href="./veiling.php?voorwerpnummer=' . $bod->voorwerpnummer . '"><h4>Veiling: ' . $bod->titel . '</h4><br></a>Uw bod: € ' . $bod->bodbedrag;
            echo '</div>';
        }
    }

    function echoVoorwerp($voorwerp, $prijs, $image)
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
                        <img src="pics/' . $image . '" alt="veilingsfoto">
                        <h4>' . $voorwerp->titel . '</h4>
                        <p>' . $beschrijving . '</p>
                        <p class="prijs">€' . $prijs . '</p>
                        <div class="veiling-info">
                            <span data-tijd="' . $voorwerp->looptijdeindeveiling . '" class="tijd"></span>
                            <button class="veiling-detail">Bied</button>
                        </div>
                    </a>
                </div>';
    }

    function echoVeilingen($username)
    {
        $voorwerpList = getVoorwerpenByVerkoper($username);
        foreach ($voorwerpList as $voorwerp) {

            $biedingen = getBiedingenByVoorwerpnummer($voorwerp->voorwerpnummer);
            if($biedingen == null){
                $prijs = $voorwerp->startprijs;
            }
            else {
                $prijs = $biedingen[0]->bodbedrag;
            }

            $foto = loadBestandByVoorwerpnummer($voorwerp->voorwerpnummer);
            echoVoorwerp($voorwerp,$prijs,$foto);
        }

    }

    ?>
