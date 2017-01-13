<?php require('partial files\models\rubriek.php');
$rubriekArray = loadAllRubrieken();
require('partial files\models\antwoord.php');
require('partial files\models\gebruiker.php');
require('partial files\models\vraag.php');



include_once('partial files\header.php'); ?>

    <h1>Wachtwoord vergeten</h1>

<?php include_once('partial files\sidebar.php');
loadRubriekenSidebar(null);

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
            $databaseAnswer = getAntwoordByUsername($username);

            if (getValidation($_POST['gebruikersnaam'])) {
                if (password_verify($answer, $databaseAnswer->antwoordtekst) && $databaseAnswer->vraagnummer == $secretQuestion) {
                    $password = generateRandomString();

                    $to      = getEmail($username);
                    $subject = 'Wachtwoord vergeten EenmaalAndermaal';
                    $message ="
                    <!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
                    <html xmlns='http://www.w3.org/1999/xhtml'>
                        <head>
                            <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
                            <title>U ben overboden!</title>
                        </head>
                        <body style='font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif; font-size: 18px'>
                            <p>Uw nieuwe wachtwoord is: ". $password ."<br>
                            Pas dit aan de volgende keer dat u inlogt door op uw profiel te klikken.</p>
                        </body>
                    </html>";

                    $headers = 'From: webmaster@eenmaalandermaal.com' . "\r\n" .
                        'Reply-To: webmaster@eenmaalandermaal.com' . "\r\n" .
                        'MIME-Version: 1.0'. "\r\n" .
                        'Content-Type: text/html; charset=ISO-8859-1' . "\r\n";


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
                $errorMessage = 'Gebruiker is niet gevalideerd.';
            }
        }
}

function echoGeheimeVragen()
{
    $vragen = getAllVragen();
    echo "<select  name='geheimeVraag'>";
    foreach ($vragen as $vraag) {
        echo "<option value = " . $vraag->vraagnummer . " >" . $vraag->tekstvraag . "</option >";

    }
    echo "</select>";
}


function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];

        if($length > 8){
            $randomString .= $characters[rand(0, 9)];
        }
    }
    return $randomString;
}

?>

    <div style="color:red" class="col-sm-12">
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
                            <?php echoGeheimeVragen(); ?>
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
<?php include_once('partial files\footer.php') ?>