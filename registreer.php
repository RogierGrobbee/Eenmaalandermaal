<?php
require('partial files\models\gebruiker.php');
require('partial files\models\rubriek.php');
require('partial files\models\vraag.php');
require('partial files\models\land.php');

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
        empty($_POST['postcode']) ||
        empty($_POST['plaats']) ||
        empty($_POST['geboortedatum']) ||
        empty($_POST['telefoon1']) ||
        empty($_POST['antwoord'])
    ) {
        $date = "$_POST[geboortedatum]";
        list($y, $m, $d) = explode('-', $date);

        $errorMessage = "Niet alles ingevuld.";
    } else {
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errorMessage = "Geen geldig emailadres.";
        } else if (doesUsernameExist($_POST['gebruikersnaam'])) {
            $errorMessage = "Gebruikersnaam bestaat al.";
        } else if (preg_match('/\s/', $_POST['gebruikersnaam'])) {
            $errorMessage = "Gebruikersnaam mag geen spaties bevatten.";
        } else if (!$uppercase || !$lowercase || !$number || strlen($password) < 8) {
            $errorMessage = "Wachtwoord moet minimaal 8 karakters lang zijn en 1 kleine letter, 1 hoofdletter en een nummer bevatten.";
        } else if ($_POST['wachtwoord'] != $_POST['wachtwoord2']) {
            $errorMessage = "Wachtwoorden komen niet overeen.";
        } else if (!validateDate($_POST['geboortedatum'])) {
            $errorMessage = "Geen geldige datum (jjjj-mm-dd).";
        } else if (date("Y-m-d", strtotime("-18 year", time())) < $_POST['geboortedatum']) {
            $errorMessage = "U moet minimaal 18 jaar oud zijn om mee te kunnen doen aan de veilingen.";
        } else if (date("Y-m-d", strtotime("1900-01-01")) > $_POST['geboortedatum']){
            $errorMessage = "Voer een geldige leeftijd in.";
        } else if (postCodeCheck($_POST['postcode']) == false) {
            $errorMessage = "Geen geldige postcode.";
        } else if (!is_numeric($_POST['telefoon1'])) {
            $errorMessage = "Telefoonnummer mag alleen bestaan uit cijfers.";
        } else if (!preg_match("/^[a-zA-Z]+$/", $_POST["plaats"])) {
            $errorMessage = "Plaats mag alleen letters bevatten.";
        } else {
            $validatieCode = generateRandomString();

            $to = $_POST['email'];
            $subject = 'Validatie EenmaalAndermaal';
            $message = 'Validatiecode: ' . $validatieCode;
            $headers = 'From: webmaster@eenmaalandermaal.com' . "\r\n" .
                'Reply-To: webmaster@eenmaalandermaal.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            mail($to, $subject, $message, $headers);

            $successMessage = "Validatie mail is verstuurd.";

            $password = hashPass($_POST['wachtwoord']);
            $antwoord = hashPass($_POST['antwoord']);

            registerGebruiker($_POST['gebruikersnaam'], $_POST['voornaam'], $_POST['achternaam'], $_POST['adres'], $_POST['postcode'],
                $_POST['plaats'], $_POST['country'], $_POST['geboortedatum'], $_POST['email'], $password, $_POST['geheimeVraag'],$_POST['telefoon1'],
                $validatieCode, $antwoord);

            header('Location: validatie.php');
        }
    }
}

function validateDate($date)
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

function postCodeCheck($postcode)
{
    $remove = str_replace(" ", "", $postcode);
    $upper = strtoupper($remove);

    if (preg_match("/^\W*[1-9]{1}[0-9]{3}\W*[a-zA-Z]{2}\W*$/", $upper)) {
        return $upper;
    } else {
        return false;
    }
}

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function echoGeheimeVragen()
{
    $vragen = getAllVragen();
    echo "<select class='form-control' name='geheimeVraag'>";
    foreach ($vragen as $vraag) {
        echo "<option value = " . $vraag->vraagnummer . " >" . $vraag->tekstvraag . "</option >";

    }
    echo "</select>";
}

function echoAllCountries()
{
    $landen = getAllLanden();
    echo "<select class='form-control' name='country'>";
    foreach ($landen as $land) {
        if ($land->landnaam == 'Nederland') {
            echo "<option selected='selected' value = " . $land->landnaam . " >" . $land->landnaam . "</option>";
        } else {
            echo "<option value = " . $land->landnaam . " >" . $land->landnaam . "</option>";
        }
    }
    echo "</select>";
}



$rubriekArray = loadAllRubrieken();

include_once('partial files\header.php');
if (!empty($_SESSION['user'])) {
    header('Location: index.php');
}

?>

    <h1>Registreer</h1>

<?php include_once('partial files\sidebar.php');
loadRubriekenSidebar(null);


?>



    <form method="post">
        <row>
            <div class="col-sm-6">
                <table class="registration-table">
                    <tr>
                        <td>Email *</td>
                        <td><input class="form-control" maxlength="255" placeholder="naam@voorbeeld.com" value="<?php if(isset($_POST['email'])){ echo $_POST['email'];}?>" type="text" name="email" ></td>
                    </tr>
                    <tr>
                        <td>Gebruikersnaam *</td>
                        <td><input class="form-control" maxlength="35" value="<?php if(isset($_POST['gebruikersnaam'])){ echo $_POST['gebruikersnaam'];}?>" type="text" name="gebruikersnaam" ></td>
                    </tr>
                    <tr>
                        <td>Voornaam *</td>
                        <td><input class="form-control" value="<?php if(isset($_POST['voornaam'])){ echo $_POST['voornaam'];}?>" type="text" name="voornaam" ></td>
                    </tr>
                    <tr>
                        <td>Achternaam *</td>
                        <td><input class="form-control" value="<?php if(isset($_POST['achternaam'])){ echo $_POST['achternaam'];}?>" type="text" name="achternaam" ></td>
                    </tr>
                    <tr>
                        <td>Wachtwoord *</td>
                        <td><input class="form-control" maxlength="100" type="password" name="wachtwoord" ></td>
                    </tr>
                    <tr>
                        <td>Bevestig wachtwoord *</td>
                        <td><input class="form-control" maxlength="100" type="password" name="wachtwoord2" ></td>
                    </tr>
                    <tr>
                        <td>Geboortedatum *</td>
                        <td><input class="form-control" placeholder="jjjj-mm-dd" value="<?php if(isset($_POST['geboortedatum'])){ echo $_POST['geboortedatum'];}?>" name="geboortedatum" type="date" data-date-inline-picker="true"/></td>
                    </tr>

                </table>
            </div>
            <div class="col-sm-6">
                <table class="registration-table">
                    <tr>
                        <td>Land</td>
                        <td>
                            <?php echoAllCountries(); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Adres *</td>
                        <td><input class="form-control" maxlength="53" value="<?php if(isset($_POST['adres'])){ echo $_POST['adres'];}?>" type="text" name="adres" ></td>
                    </tr>
                    <tr>
                        <td>Plaats *</td>
                        <td><input class="form-control" maxlength="100" value="<?php if(isset($_POST['plaats'])){ echo $_POST['plaats'];}?>" type="text" name="plaats" ></td>
                    </tr>
                    <tr>
                        <td>Postcode *</td>
                        <td><input class="form-control" placeholder="1234AB" value="<?php if(isset($_POST['postcode'])){ echo $_POST['postcode'];}?>" type="text" name="postcode" ></td>
                    </tr>
                    <tr>
                        <td>Telefoon *</td>
                        <td><input class="form-control" maxlength="15" value="<?php if(isset($_POST['telefoon1'])){ echo $_POST['telefoon1'];}?>" type="text" name="telefoon1"></td>
                    </tr>
                    <tr>
                        <td>Geheime vraag</td>
                        <td>
                            <?php echoGeheimeVragen(); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Antwoord *</td>
                        <td><input class="form-control" maxlength="100" value="<?php if(isset($_POST['antwoord'])){ echo $_POST['antwoord'];}?>" type="text" name="antwoord"></td>
                    </tr>

                </table>
            </div>
        </row>
        <row>

            <div class="col-sm-12 submit-registrion orangeButton">
                <h4>velden met een <b>*</b> zijn verplicht</h4>
                <input  type="submit" name="registreer" value="Registreren">
            </div>

        </row>
    </form>
    <br><br>

    <row>
        <div style="color:red" class="col-sm-12">
            <br>

            <?php
            if (!empty($errorMessage)) {
                echo "<div class='alert alert-danger'>";
                echo $errorMessage;
                echo "</div>";
            }
            ?>

        </div>
    </row>


<?php include_once('partial files\footer.php') ?>