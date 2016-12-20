<?php

include_once('..\partial files\header.php'); ?>

    <h1>Profiel</h1>
<?php
loadSidebar();
if (isset($_SESSION["user"])) {
    $username = $_SESSION["user"];
}
$username = 'jasper';

include_once ('..\partial files\databaseconnection.php');
$user = getUserByUsername($username);

?>
    <div class="col-sm-12">

<!--        <h3>Overzicht</h3>-->
<!--        <div class="well">-->
<!--            Username: --><?php //echo $user->gebruikersnaam; ?>
<!--            <br>Naam: --><?php //echo $user->voornaam . " " . $user->achternaam ?>
<!--            <br>Email: --><?php //echo $user->email ?>
<!--            --><?php //echoPhoneNumbers($phoneNumbers) ?>
<!--        </div>-->
<!--        <h3>Adres</h3>-->
<!--        <div class="well">-->
<!--            Adres: --><?php //echo $user->adresregel1; ?>
<!--            <br>Postcode: --><?php //echo $user->postcode; ?>
<!--            <br>Plaats: --><?php //echo $user->plaatsnaam; ?>
<!--            <br>Land: --><?php //echo $user->land; ?>
<!--        </div>-->
<!--        <h3>Bod historie</h3>-->
<!---->
<!--        --><?php //echoBiedingen($username) ?>


    </div>

<?php
include_once('..\partial files\footer.php');

function echoPhoneNumbers($phoneNumbers){
    echo '<br><br>';
    global $username;

    echo 'Telefoonnummers:';
    foreach ($phoneNumbers as $k => $bod) {
        echo '<br>'.$bod->telefoon;
    }

    if(isset($_POST['telefoon'])){
        if (is_numeric( $_POST['telefoon'])) {
            $temp = $phoneNumbers[count($phoneNumbers ) - 1];
            $hoogsteVolgnr = $temp->volgnr;
            $hoogsteVolgnr++;

            addPhoneNumber($hoogsteVolgnr, $username, $_POST['telefoon']);
            echo '<br>'.$_POST['telefoon'];
        }
    }


   ?>
<form method="post">
    <br>
        <div class="col-sm-6 col-xs-8">

                    <input maxlength="15" value="<?php if(isset($_POST['telefoon'])){ echo $_POST['telefoon'];}?>" type="text" name="telefoon" >
        </div>
        <div class="col-sm-1 col-xs-2 submit-registrion">
            <button type="submit" name="Toevoegen" value="toevoegen">Nummer toevoegen</button>
        </div>
    <br>
    </form>
<?php
}

function echoBiedingen($username)
{
    $biedingen = getBiedingenByUsername($username);
    foreach ($biedingen as $k => $bod) {
        echo '<div class="well">';
        echo '<a href="./veiling.php?voorwerpnummer='.$bod->voorwerpnummer.'"><h4>Veiling: '.$bod->titel.'</h4><br></a>Uw bod: € ' . $bod->bodbedrag;
        echo '</div>';

    }

}

?>



<?php
function loadSidebar()
{
    echo '<div class="list-group col-sm-3">';
    echo "<ul class='topnav' id='myTopnav''>";
    echo "<li><strong><div class='list-group-item rubriekList' href='#'>Rubrieken</div></strong></li>";

    echo '<li><a class="list-group-item active" href=overzicht.php#>Overzicht</a></li>';
    echo '<li><a class="list-group-item" href=overzicht.php#>Wachtwoord wijzigen</a></li>';
    echo '<li><a class="list-group-item" href=overzicht.php#>Verkoper worden</a></li>';

    echo "<li class='icon''>";
    echo "<a href='javascript:void(0);' onclick='myFunction()''>&#9776;</a>";
    echo "</li>";
    echo "</ul>";
    echo '</div><div class="col-sm-9">';
}

?>
