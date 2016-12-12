<?php include_once('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();
include_once('partial files\header.php'); ?>

    <h1>Registreer</h1>

<?php include_once('partial files\sidebar.php');
loadSidebar($rubriekArray, null);
$errorMessage = "";
$successMessage = "";
if (!empty($_POST['wachtwoord'])) {
    $password = $_POST['wachtwoord'];

    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number = preg_match('@[0-9]@', $password);
}
if (isset($_POST['registreer'])) {
    if (empty($_POST['email']) ||
        empty($_POST['gebruikersnaam']) ||
        empty($_POST['voornaam']) ||
        empty($_POST['achternaam']) ||
        empty($_POST['wachtwoord']) ||
        empty($_POST['wachtwoord2']) ||
        empty($_POST['adres']) ||
        empty($_POST['plaats']) ||
        empty($_POST['telefoon1']) ||
        empty($_POST['antwoord'])
) {
        $errorMessage = "Niet alles ingevuld.";
    } else
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Geen geldig emailadres.";
    } else if (doesUsernameAlreadyExist($_POST['gebruikersnaam'])) {
        $errorMessage = "Gebruikersnaam bestaat al.";
    } else if (!$uppercase || !$lowercase || !$number || strlen($password) < 8) {
        $errorMessage = "Wachtwoord moet minimaal 8 character lang zijn en 1 kleine letter, 1 hoofdletter en een nummer bevatten.";
    } else if ($_POST['wachtwoord'] != $_POST['wachtwoord2']) {
        $errorMessage = "Wachtwoorden komen niet overeen.";
    } else if (postCodeCheck($_POST['postcode']) == false) {
        $errorMessage = "Geen geldige postcode.";
    } else if ($_POST['telefoon1'] == $_POST['telefoon2']) {
        $errorMessage = "Dubbel telefoonnummer.";
    } else {
        $validatieCode = generateRandomString();
        $to      = $_POST['email'];
        $subject = 'Validatie eenmaalAndermaal';
        $message = 'Validatie code: ' . $validatieCode;
        $headers = 'From: eenmaalAndermaal';
        mail($to, $subject, $message, $headers);

        $successMessage = "Validatie mail verstuurd.";
    }
}

?>

    <form method="post">
        <row>
            <div class="col-sm-6">
                <table class="registration-table">
                    <tr>
                        <td>Email</td>
                        <td><input type="text" name="email" required></td>
                    </tr>
                    <tr>
                        <td>Gebruikersnaam</td>
                        <td><input type="text" name="gebruikersnaam" required></td>
                    </tr>
                    <tr>
                        <td>Voornaam</td>
                        <td><input type="text" name="voornaam" required></td>
                    </tr>
                    <tr>
                        <td>Achternaam</td>
                        <td><input type="text" name="achternaam" required></td>
                    </tr>
                    <tr>
                        <td>Wachtwoord</td>
                        <td><input type="password" name="wachtwoord" required></td>
                    </tr>
                    <tr>
                        <td>Wachtwoord 2</td>
                        <td><input type="password" name="wachtwoord2" required></td>
                    </tr>
                    <tr>
                        <td>Geboortedatum</td>
                        <td><input type="date" data-date-inline-picker="true"/></td>
                    </tr>

                </table>
            </div>
            <div class="col-sm-6">
                <table class="registration-table">
                    <tr>
                        <td>Land</td>
                        <td>
                            <?php echo returnAllCountries(); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Adres</td>
                        <td><input type="text" name="adres" required></td>
                    </tr>
                    <tr>
                        <td>Plaats</td>
                        <td><input type="text" name="plaats" required></td>
                    </tr>
                    <tr>
                        <td>Postcode</td>
                        <td><input type="text" name="postcode"required></td>
                    </tr>
                    <tr>
                        <td>Telefoon 1</td>
                        <td><input type="text" name="telefoon1"required></td>
                    </tr>
                    <tr>
                        <td>Telefoon 2</td>
                        <td><input type="text" name="telefoon2" ></td>
                    </tr>
                    <tr>
                        <td>Geheime vraag</td>
                        <td>
                            <?php echo returnGeheimeVragen(); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Antwoord</td>
                        <td><input type="text" name="antwoord"required></td>
                    </tr>

                </table>
            </div>
        </row>

        <row>
            <div class="col-sm-12 submit-registrion">
                <input type="submit" name="registreer" value="Registreren">
                <form action="index.php">
                    <input type="submit" value="Annuleren"/>
                </form>
            </div>

        </row>
    </form>
    <br><br>
    <row>
        <div class="col-sm-12">
            <br>

        </div>
    </row>
    <row>
        <div style="color:red" class="col-sm-12">
            <?php
            echo $errorMessage;
            echo $successMessage;
            ?>
            <br><br>
        </div>

    </row>
<?php include_once('partial files\footer.php') ?>