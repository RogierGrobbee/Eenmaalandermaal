<?php

include_once('..\partial files\header.php');
include_once('..\partial files\models\gebruiker.php');
include_once('..\partial files\models\voorwerp.php');
include_once('..\partial files\models\gebruikerstelefoon.php');

$username = "";
if (isset($_GET["user"])) {
    $username = $_GET["user"];
}
else {
    if (isset($_SESSION["user"])){
    $username = $_SESSION["user"];
    }
}
$sessionUsername = "";
if (isset($_SESSION["user"])) {
    $sessionUsername = "Rogier"; //$_SESSION["user"];
}
$loggedIn = false;
if ($username == $sessionUsername) {
    $loggedIn = true;
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
        <?php loadSidebar($username) ?>
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
    } else {?>

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
        <div class="well">
            Hier komen alle actieve veilingen van de gebruiker
        </div>


    <?php
    }
}


include_once('..\partial files\footer.php');

function echoPhoneNumbers()
{
    echo '<br>';
    global $username;

    $phoneNumbersObjects = getPhoneNumbers($username);
    if (isset( $_POST['remove'])){
        foreach ($phoneNumbersObjects as $k => $number) {
            if ($number->telefoon == $_POST['remove']){
                unset($phoneNumbersObjects[$k]);
                removePhoneNumber($username, $_POST['remove']);
            }
        }
    }
    $phoneNumbersObjects = getPhoneNumbers($username);
    $phoneNumbers = array();
    echo 'Telefoonnummers:';
    if (count($phoneNumbersObjects) < 2 && !isset($_POST['telefoon'])){
        echo '<div class="row"><p style="float: left; margin-left: 20px">' . $phoneNumbersObjects[0]->telefoon.'</p></div>';
    }
    else{
        foreach ($phoneNumbersObjects as $k => $number) {
            echo '<div class="row"><p style="float: left; margin-left: 20px">' . $number->telefoon. '</p> <form method="post" style="float:left" ><button style="float:left" type="submit" name="remove" value="'.$number->telefoon.'">X</button></form></div>';
            array_push($phoneNumbers, $number->telefoon);
        }
        if (isset($_POST['telefoon'])){
        if (is_numeric($_POST['telefoon'])) {
            if (!in_array($_POST['telefoon'], $phoneNumbers))
            {
                $temp = $phoneNumbersObjects[count($phoneNumbersObjects) - 1];
                $hoogsteVolgnr = $temp->volgnr;
                $hoogsteVolgnr++;

                insertPhoneNumber($hoogsteVolgnr, $username, $_POST['telefoon']);
                echo '<div class="row"><p style="float: left; margin-left: 20px">'.$_POST['telefoon'].'</p><form method="post" style="float: left"><button style="float: left" type="submit" name="remove" value="'.$_POST['telefoon'].'">X</button> </form></div>';
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
            <button class="btn-add-number" type="submit" name="Toevoegen" value="toevoegen">Nummer toevoegen</button>
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

function loadSidebar($username)
{

    echo '<div class="list-group col-sm-3">';
    echo "<ul class='topnav' id='myTopnav''>";
    echo "<li><strong><div class='list-group-item rubriekList' href='#'>Menu</div></strong></li>";

    echo '<li><a class="list-group-item active" href=overzicht.php?user='.$username.'>Overzicht</a></li>';
    echo '<li><a class="list-group-item" href=overzicht.php#>Wachtwoord wijzigen</a></li>';
    echo '<li><a class="list-group-item" href=biedingen.php?user='.$username.'>Bod historie</a></li>';
    echo '<li><a class="list-group-item" href=overzicht.php#>Verkoper worden</a></li>';

    echo "<li class='icon''>";
    echo "</li>";
    echo "</ul>";
    echo '</div><div class="col-sm-9">';
}
?>