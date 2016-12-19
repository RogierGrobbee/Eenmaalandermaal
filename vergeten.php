<?php include_once('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();
include_once('partial files\header.php'); ?>

    <h1>Wachtwoord vergeten</h1>

<?php include_once('partial files\sidebar.php');
loadSidebar($rubriekArray, null);

$errorMessage;
$successMessage;

if (isset($_POST['Vergeten'])) {
    if (
        empty($_POST['gebruikersnaam']) ||
        empty($_POST['antwoord'])
    ) {
        $errorMessage = "Niet alles ingevuld.";
    } else
        if (preg_match('/\s/',$_POST['gebruikersnaam'])) {
            $errorMessage = "Gebruikersnaam mag geen spaties bevatten.";
        } else {
            $secretQuestion = $_POST['geheimeVraag'];
            $answer = strtolower($_POST['antwoord']);
            $username = $_POST['gebruikersnaam'];
            $hash = getSecretAnswer($username);
            if (getValidation($_POST['gebruikersnaam'])) {
                if (password_verify($answer, $hash) && getQuestionNumber($username) == $secretQuestion) {
                    $password = generateRandomString();
                    $to      = getEmail($username);
                    $subject = 'Wachtwoord vergeten EenmaalAndermaal';
                    $message = 'Uw nieuwe wachtwoord: ' . $password;
                    $headers = 'From: webmaster@eenmaalandermaal.com' . "\r\n" .
                        'Reply-To: webmaster@eenmaalandermaal.com' . "\r\n" .
                        'X-Mailer: PHP/' . phpversion();

                    $hashedPassword = hashPass($password);

                    mail($to, $subject, $message, $headers);
                    $sql = "UPDATE gebruiker SET wachtwoord = :wachtwoord WHERE gebruikersnaam = :gebruikersnaam";
                    $stmt = $db->prepare($sql);
                    $stmt->bindValue(':gebruikersnaam', $username, PDO::PARAM_STR);
                    $stmt->bindValue(':wachtwoord', $hashedPassword, PDO::PARAM_STR);
                    $stmt->execute();

                    $successMessage = "Uw nieuwe wachtwoord is per e-mail verstuurd.";
                } else {
                    $errorMessage = 'Combinatie gebruikersnaam, geheime vraag en antwoord zijn onjuist.';
                }
            } else {
                $message = 'Gebruiker is niet gevalideerd.';
            }
        }
}

?>
    <form method="post">
        <row>
            <div class="col-sm-6">
                <table class="registration-table">
                    <tr>
                        <td>Gebruikersnaam</td>
                        <td><input pattern="[a-zA-Z0-9-]+" value="<?php if(isset($_POST['gebruikersnaam'])){ echo $_POST['gebruikersnaam'];}?>" type="text" name="gebruikersnaam" ></td>
                    </tr>
                    <tr>
                        <td>Geheime vraag</td>
                        <td>
                            <?php echo returnGeheimeVragen(); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Antwoord</td>
                        <td><input  value="<?php if(isset($_POST['antwoord'])){ echo $_POST['antwoord'];}?>" type="text" name="antwoord"></td>
                    </tr>
                </table>
            </div>
        </row>
        <row>
            <div class="col-sm-12 submit-registrion">
                <input type="submit" name="Vergeten" value="Wachtwoord aanvragen">
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
            elseif (!empty($successMessage)) {
                echo "<div class='alert alert-success'>";
                echo $successMessage;
                echo "</div>";
            }
            ?>
        </div>

    </row>
<?php include_once('partial files\footer.php') ?>