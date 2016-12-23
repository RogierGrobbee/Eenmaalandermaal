<?php include_once('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();
include_once('partial files\header.php'); ?>

    <h1>Profiel</h1>

<?php include_once('partial files\sidebar.php');
loadRubriekenSidebar(null);
if (isset($_SESSION["user"])) {
    $username = $_SESSION["user"];
}
//$username = 'jasper';
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
    <div class="row">
        <div class="col-lg-3 col-md-5 col-sm-7 col-xs-12 add-number">
            <input class="input-add-number" maxlength="15" value="<?php if(isset($_POST['telefoon'])){ echo $_POST['telefoon'];}?>" type="text" name="telefoon" >
        </div>
        <div class="col-lg-3 col-md-4 col-sm-5 col-xs-12 add-number">
            <button class="btn-add-number" type="submit" name="Toevoegen" value="toevoegen">Nummer toevoegen</button>
        </div>
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