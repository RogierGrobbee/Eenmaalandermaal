<?php include_once('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();
include_once('partial files\header.php'); ?>

    <h1>Wachtwoord vergeten</h1>

<?php include_once('partial files\sidebar.php');
loadSidebar($rubriekArray, null);

$message = "";
if (isset($_POST['Vergeten'])) {
    if (
        empty($_POST['gebruikersnaam']) ||
        empty($_POST['antwoord'])
    ) {
        $message = "Niet alles ingevuld.";
    } else
        if (preg_match('/\s/',$_POST['gebruikersnaam'])) {
            $message = "Gebruikersnaam mag geen spaties bevatten.";
        } else {
            $secretQuestion = $_POST['geheimeVraag'];
            $answer = strtolower($_POST['antwoord']);
            $username = $_POST['gebruikersnaam'];
            $hash = getSecretAnswer($username);
            if(getQuestionNumber($_POST['gebruikersnaam']) == $secretQuestion){
                if (getValidation($_POST['gebruikersnaam'])) {
                    if (password_verify($answer, $hash) && getQuestionNumber($_POST['gebruikersnaam']) == $secretQuestion) {
                        //actie
                        echo 'test';
                    } else {
                        $message = 'Combinatie gebruikersnaam, geheime vraag en antwoord zijn onjuist.';
                    }
                } else {
                    $message = 'Gebruiker is niet gevalideerd.';
                }
            }
            else {
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
            <?php
            echo $errorMessage;
            ?>
            <br><br>
        </div>
    </row>

<?php include_once('partial files\footer.php') ?>