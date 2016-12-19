<?php include_once('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();
include_once('partial files\header.php'); ?>

    <h1>Profiel</h1>

<?php include_once('partial files\sidebar.php');
loadSidebar($rubriekArray, null);
if (isset($_SESSION["user"])) {
    $username = $_SESSION["user"];
}

$user = getUserByUsername($username);
$phoneNumbers = getPhoneNumbers($username);
?>
    <div class="col-sm-13">
        <h3>Overzicht</h3>
        <div class="well">
            Username: <?php echo $user->gebruikersnaam; ?>
            <br>Naam: <?php echo $user->voornaam . " " . $user->achternaam ?>
            <br>Email: <?php echo $user->email ?>
            <?php echoPhoneNumbers($phoneNumbers) ?>
        </div>
        <h3>Adres</h3>
        <div class="well">
            Adres: <?php echo $user->adresregel1; ?>
            <br>Postcode: <?php echo $user->postcode; ?>
            <br>Plaats: <?php echo $user->plaatsnaam; ?>
            <br>Land: <?php echo $user->land; ?>
        </div>
        <h3>Bod historie</h3>

        <?php echoBiedingen($username) ?>


    </div>

<?php
include_once('partial files\footer.php');

function echoPhoneNumbers($phoneNumbers){
    echo '<br><br>';
    foreach ($phoneNumbers as $k => $bod) {
        echo 'Telefoonnummers:<br>';
        echo $bod->telefoon;
    }
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