<?php include_once('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();
include_once('partial files\header.php'); ?>

    <h1>Wachtwoord vergeten</h1>

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
            if(getValidation($_POST['gebruikersnaam'])) {
                $secretQuestion = $_POST['geheimeVraag'];
                $answer = $_POST['antwoord'];
                $username = $_POST['gebruikersnaam'];
                $hash = getSecretAnswer($username);
                if (password_verify($password, $hash)) {
                    //actie
                    echo 'test';
                } else {
                    $errorMessage = 'Combinatie gebruikersnaam en wachtwoord zijn onjuist.';
                }
            }
            else{
                $errorMessage = 'Gebruiker is niet gevalideerd.';
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
            <?php
            echo $errorMessage;
            echo $successMessage;
            ?>
            <br><br>
        </div>
    </row>

<?php include_once('partial files\footer.php') ?>