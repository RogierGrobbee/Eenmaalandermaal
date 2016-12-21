<?php include_once('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();
include_once('partial files\header.php');
//cantVisitLoggedIn();


    function returnPaymentMethode()
    {
        global $db;
        $query = $db->query("SELECT betalingswijze FROM betalingswijze");
        echo "<select class='form-control' name='payment'>";
        foreach ($query as $row) {
            if ($row['betalingswijze'] == 'Contant') {
                echo "<option selected='selected' value = " . $row['betalingswijze'] . " >" . $row['betalingswijze'] . "</option>";
            } else {
                echo "<option value = " . $row['betalingswijze'] . " >" . $row['betalingswijze'] . "</option>";
            }
        }
        echo "</select>";
    }
    function returnDuration()
    {
        global $db;
        $query = $db->query("SELECT looptijd FROM looptijd");
        echo "<select class='form-control' name='Duration'>";
        foreach ($query as $row) {
            if ($row['looptijd'] == 7) {
                echo "<option selected='selected' value = " . $row['looptijd'] . " >" . $row['looptijd'] . "</option>";
            } else {
                echo "<option value = " . $row['looptijd'] . " >" . $row['looptijd'] . "</option>";
            }
        }
        echo "</select>";
    }



$errorMessage = "";
$successMessage = "";
if (isset($_POST['toevoegen'])) {
    if (empty($_POST['titel']) ||
        empty($_POST['beschrijving']) ||
        empty($_POST['startprijs']) ||
        empty($_POST['plaatsnaam'])
    ) {
        $errorMessage = "Niet alles ingevuld.";
    }
    else if(!is_numeric($_POST['startprijs'])){
        $errorMessage = "Startprijs mag alleen cijfers bevatten.";
    }
    else if (!preg_match("/^[a-zA-Z]+$/", $_POST["plaatsnaam"])){
        $errorMessage = "Plaatsnaam mag alleen letters bevatten.";
    }
    else if(!empty($_POST['verzendkosten'])){
        if(!is_numeric($_POST['verzendkosten'])) {
                $errorMessage = "Startprijs mag alleen cijfers bevatten.";
        }
    }
    else
        {
            $description = htmlspecialchars($_POST['beschrijving']);
            $title = htmlspecialchars($_POST['titel']);
            $city = htmlspecialchars($_POST['plaatsnaam']);
            $user = $_SESSION['user'];

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
        }
}






?>

    <h1>Veiling Toevoegen</h1>

<?php include_once('partial files\sidebar.php');
loadSidebar($rubriekArray, null);
?>


    <form method="post">
        <row>
            <div class="form-group">
                <table class="registration-table">
                    <tr>
                        <td>Titel</td>
                        <td><input class="form-control" maxlength="100" value="<?php if(isset($_POST['titel'])){ echo $_POST['titel'];}?>" type="text" name="titel" ></td>
                    </tr>
                    <tr>
                        <td>Beschrijving</td>
                        <td>
                            <textarea class="form-control" maxlength="8000" rows="5" name="beschrijving"><?php if(isset($_POST['beschrijving'])){ echo $_POST['beschrijving'];}?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>Startprijs</td>
                        <td><input class="form-control" value="<?php if(isset($_POST['startprijs'])){ echo $_POST['startprijs'];}?>" type="text" name="startprijs" ></td>
                    </tr>
                    <tr>
                        <td>Betalingswijze</td>
                        <td>
                            <?php echo returnPaymentMethode();?>
                        </td>
                    </tr>
                    <tr>
                        <td>Betalingsinstructie</td>
                        <td><input class="form-control" maxlength="255" value="<?php if(isset($_POST['betalingsinstructie'])){ echo $_POST['betalingsinstructie'];}?>" type="text" name="betalingsinstructie" ></td>
                    </tr>
                    <tr>
                        <td>Plaatsnaam</td>
                        <td><input class="form-control" maxlength="30" value="<?php if(isset($_POST['plaatsnaam'])){ echo $_POST['plaatsnaam'];}?>" type="text" name="plaatsnaam" ></td>
                    </tr>
                    <tr>
                        <td>Looptijd</td>
                        <td>
                            <?php echo  returnDuration();?>
                        </td>
                    </tr>
                    <tr>
                        <td>Land</td>
                        <td>
                            <?php echo returnAllCountries(); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Verzendkosten</td>
                        <td><input class="form-control" maxlength="5" value="<?php if(isset($_POST['verzendkosten'])){ echo $_POST['verzendkosten'];}?>" type="text" name="adres" ></td>
                    </tr>
                    <tr>
                        <td>Verzendinstructies</td>
                        <td><input class="form-control" maxlength="255" value="<?php if(isset($_POST['verzendinstructies'])){ echo $_POST['verzendinstructies'];}?>" type="text" name="plaats" ></td>
                    </tr>
                    <tr>
                        <td>Afbeelding</td>
                        <td><input class="form-control" maxlength="15" value="<?php if(isset($_POST['afbeelding'])){ echo $_POST['afbeelding'];}?>" type="text" name="telefoon1"></td>
                    </tr>
                </table>
            </div>
        </row>

        <row>
            <div class="col-sm-12 submit-registrion orangeButton">
                <input  type="submit" name="toevoegen" value="Toevoegen">
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