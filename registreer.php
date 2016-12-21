<?php include_once('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();

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
    } else
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errorMessage = "Geen geldig emailadres.";
        } else if (doesUsernameAlreadyExist($_POST['gebruikersnaam'])) {
            $errorMessage = "Gebruikersnaam bestaat al.";
        } else if (preg_match('/\s/',$_POST['gebruikersnaam'])) {
            $errorMessage = "Gebruikersnaam mag geen spaties bevatten.";
        } else if (!$uppercase || !$lowercase || !$number || strlen($password) < 8) {
            $errorMessage = "Wachtwoord moet minimaal 8 characters lang zijn en 1 kleine letter, 1 hoofdletter en een nummer bevatten.";
        } else if ($_POST['wachtwoord'] != $_POST['wachtwoord2']) {
            $errorMessage = "Wachtwoorden komen niet overeen.";
        } else if (!validateDate($_POST['geboortedatum'])) {
            $errorMessage = "Geen geldige datum (jjjj-mm-dd).";
        } else if(date("Y-m-d", strtotime("-18 year", time())) < $_POST['geboortedatum']){
            $errorMessage = "U moet minimaal 18 jaar oud zijn om mee te kunnen doen aan de veilingen.";
        } else if (postCodeCheck($_POST['postcode']) == false) {
            $errorMessage = "Geen geldige postcode.";
        }  else if (!is_numeric($_POST['telefoon1'])) {
            $errorMessage = "Telefoonnummer mag alleen bestaan uit cijfers.";
        }
        else
        {
            $validatieCode = generateRandomString();
            $to      = $_POST['email'];
            $subject = 'Validatie EenmaalAndermaal';
            $message = 'Validatiecode: ' . $validatieCode;
            $headers = 'From: webmaster@eenmaalandermaal.com' . "\r\n" .
                'Reply-To: webmaster@eenmaalandermaal.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            mail($to, $subject, $message, $headers);

            $successMessage = "Validatie mail is verstuurd.";

            $password = hashPass($_POST['wachtwoord']);

            global $db;
            $sql = "INSERT INTO gebruiker (gebruikersnaam, voornaam, achternaam, adresregel1, postcode, plaatsnaam, land, geboortedatum, email, wachtwoord, verkoper, vraag, gevalideerd) VALUES
                (:username, :firstname, :lastname, :adres, :postcode, :plaatsnaam, :land, :geboortedatum, :email, :wachtwoord, :verkoper, :vraag, :gevalideerd)";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':username', $_POST['gebruikersnaam'], PDO::PARAM_STR);
            $stmt->bindValue(':firstname', $_POST['voornaam'], PDO::PARAM_STR);
            $stmt->bindValue(':lastname', $_POST['achternaam'], PDO::PARAM_STR);
            $stmt->bindValue(':adres', $_POST['adres'], PDO::PARAM_STR);
            $stmt->bindValue(':postcode', $_POST['postcode'], PDO::PARAM_STR);
            $stmt->bindValue(':plaatsnaam', $_POST['plaats'], PDO::PARAM_STR);
            $stmt->bindValue(':land', $_POST['country'], PDO::PARAM_STR);                 //////////////////
            $stmt->bindValue(':geboortedatum', $_POST['geboortedatum'], PDO::PARAM_STR);
            $stmt->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
            $stmt->bindValue(':wachtwoord', $password, PDO::PARAM_STR);
            $stmt->bindValue(':verkoper', 0, PDO::PARAM_INT);
            $stmt->bindValue(':vraag', $_POST['geheimeVraag'], PDO::PARAM_INT);                 //////////////////
            $stmt->bindValue(':gevalideerd', 0, PDO::PARAM_INT);
            $stmt->execute();

            $sql2 = "INSERT INTO validation (gebruikersnaam, validatiecode) VALUES
                (:gebruiker, :validate)";
            $stmt = $db->prepare($sql2);
            $stmt->bindValue(':gebruiker', $_POST['gebruikersnaam'], PDO::PARAM_STR);
            $stmt->bindValue(':validate', $validatieCode, PDO::PARAM_STR);
            $stmt->execute();


            $antwoord = $_POST['antwoord'];
            $sql3 = "INSERT INTO antwoord (vraagnummer, gebruikersnaam, antwoordtekst) VALUES
                (:nummer, :gebruikersnaam, :antwoord)";
            $stmt = $db->prepare($sql3);
            $stmt->bindValue(':nummer', $_POST['geheimeVraag'], PDO::PARAM_STR);
            $stmt->bindValue(':gebruikersnaam', $_POST['gebruikersnaam'], PDO::PARAM_STR);
            $stmt->bindValue(':antwoord', hashPass($antwoord), PDO::PARAM_STR);
            $stmt->execute();


            $sql4 = "INSERT INTO gebruikerstelefoon (volgnr, gebruikersnaam, telefoon) VALUES
                (:nummer, :gebruikersnaam, :tel)";
            $stmt = $db->prepare($sql4);
            $stmt->bindValue(':nummer', 0, PDO::PARAM_STR);
            $stmt->bindValue(':gebruikersnaam', $_POST['gebruikersnaam'], PDO::PARAM_STR);
            $stmt->bindValue(':tel', $_POST['telefoon1'], PDO::PARAM_STR);
            $stmt->execute();

            header('Location: validatie.php');
        }
}


include_once('partial files\header.php');
cantVisitLoggedIn();
?>

    <h1>Registreer</h1>

<?php include_once('partial files\sidebar.php');
loadSidebar($rubriekArray, null);


?>



    <form method="post">
        <row>
            <div class="col-sm-6">
                <table class="registration-table">
                    <tr>
                        <td>Email</td>
                        <td><input maxlength="255" placeholder="name@example.com" value="<?php if(isset($_POST['email'])){ echo $_POST['email'];}?>" type="text" name="email" ></td>
                    </tr>
                    <tr>
                        <td>Gebruikersnaam</td>
                        <td><input maxlength="35" value="<?php if(isset($_POST['gebruikersnaam'])){ echo $_POST['gebruikersnaam'];}?>" type="text" name="gebruikersnaam" ></td>
                    </tr>
                    <tr>
                        <td>Voornaam</td>
                        <td><input value="<?php if(isset($_POST['voornaam'])){ echo $_POST['voornaam'];}?>" type="text" name="voornaam" ></td>
                    </tr>
                    <tr>
                        <td>Achternaam</td>
                        <td><input  value="<?php if(isset($_POST['achternaam'])){ echo $_POST['achternaam'];}?>" type="text" name="achternaam" ></td>
                    </tr>
                    <tr>
                        <td>Wachtwoord</td>
                        <td><input maxlength="100" type="password" name="wachtwoord" ></td>
                    </tr>
                    <tr>
                        <td>Bevestig wachtwoord</td>
                        <td><input maxlength="100" type="password" name="wachtwoord2" ></td>
                    </tr>
                    <tr>
                        <td>Geboortedatum</td>
                        <td><input  placeholder="jjjj-mm-dd" value="<?php if(isset($_POST['geboortedatum'])){ echo $_POST['geboortedatum'];}?>" name="geboortedatum" type="date" data-date-inline-picker="true"/></td>
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
                        <td><input maxlength="53" value="<?php if(isset($_POST['adres'])){ echo $_POST['adres'];}?>" type="text" name="adres" ></td>
                    </tr>
                    <tr>
                        <td>Plaats</td>
                        <td><input maxlength="100" value="<?php if(isset($_POST['plaats'])){ echo $_POST['plaats'];}?>" type="text" name="plaats" ></td>
                    </tr>
                    <tr>
                        <td>Postcode</td>
                        <td><input  placeholder="1234AB" value="<?php if(isset($_POST['postcode'])){ echo $_POST['postcode'];}?>" type="text" name="postcode" ></td>
                    </tr>
                    <tr>
                        <td>Telefoon</td>
                        <td><input maxlength="15" value="<?php if(isset($_POST['telefoon1'])){ echo $_POST['telefoon1'];}?>" type="text" name="telefoon1"></td>
                    </tr>
                    <tr>
                        <td>Geheime vraag</td>
                        <td>
                            <?php echo returnGeheimeVragen(); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Antwoord</td>
                        <td><input maxlength="100" value="<?php if(isset($_POST['antwoord'])){ echo $_POST['antwoord'];}?>" type="text" name="antwoord"></td>
                    </tr>

                </table>
            </div>
        </row>

        <row>
            <div class="col-sm-12 submit-registrion orangeButton">
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