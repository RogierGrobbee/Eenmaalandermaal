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
                $errorMessage = "Verzendkosten mag alleen cijfers bevatten.";
        }
    }
    else
        {
            $titel = htmlspecialchars($_POST['titel']);
            $beschrijving = htmlspecialchars($_POST['beschrijving']);
            $plaatsnaam = htmlspecialchars($_POST['plaatsnaam']);
            $verzendinstructies;
            $betalingsinstructie;
            if(!empty($_POST['betalingsinstructie'])){
                $betalingsinstructie = htmlspecialchars($_POST['betalingsinstructie']);
            }
            elseif (!empty($_POST['verzendinstructies'])){
                $verzendinstructies = htmlspecialchars($_POST['verzendinstructies']);
            }

            global $db;
            $sql = "INSERT INTO voorwerp (titel, beschrijving, startprijs, betalingswijze, betalingsinstructie, plaatsnaam, land, looptijd, verzendkosten, verzendinstructies, verkoper)  VALUES
                (:titel, :beschrijving, :startprijs, :betalingswijze, :betalingsinstructie, :plaatsnaam, :land, :looptijd, :verzendkosten, :verzendinstructies, :verkoper)";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':titel', $titel, PDO::PARAM_STR);
            $stmt->bindValue(':beschrijving', $beschrijving, PDO::PARAM_STR);
            $stmt->bindValue(':startprijs', $_POST['startprijs'], PDO::PARAM_STR);
            $stmt->bindValue(':betalingswijze', $_POST['payment'], PDO::PARAM_STR);
            $stmt->bindValue(':betalingsinstructie', $betalingsinstructie, PDO::PARAM_STR);
            $stmt->bindValue(':plaatsnaam', $plaatsnaam, PDO::PARAM_STR);
            $stmt->bindValue(':land', $_POST['country'], PDO::PARAM_STR);
            $stmt->bindValue(':looptijd', $_POST['Duration'], PDO::PARAM_STR);
            $stmt->bindValue(':verzendkosten',  $_POST['verzendkosten'], PDO::PARAM_STR);
            $stmt->bindValue(':verzendinstructies', $verzendinstructies, PDO::PARAM_STR);
            $stmt->bindValue(':verkoper', $_SESSION['user'], PDO::PARAM_INT);
            $stmt->execute();
        }
    $successMessage="Veiling is toegevoegd";

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
                        <td><input class="form-control" maxlength="5" value="<?php if(isset($_POST['verzendkosten'])){ echo $_POST['verzendkosten'];}?>" type="text" name="verzendkosten" ></td>
                    </tr>
                    <tr>
                        <td>Verzendinstructies</td>
                        <td><input class="form-control" maxlength="255" value="<?php if(isset($_POST['verzendinstructies'])){ echo $_POST['verzendinstructies'];}?>" type="text" name="verzendinstructies" ></td>
                    </tr>
<!--                    <tr>-->
<!--                        <td>Afbeelding</td>-->
<!--                        <td><input class="form-control" maxlength="15" value="--><?php //if(isset($_POST['afbeelding'])){ echo $_POST['afbeelding'];}?><!--" type="text" name="telefoon1"></td>-->
<!--                    </tr>-->
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

    <div class="row" style="margin-top: -22.5px;">
    <br>
    <?php
    if (!empty($errorMessage)) {
        echo "<div class='alert alert-danger error'>$errorMessage</div>";
    }
    else if(!empty($successMessage)){
        echo "<div class='alert alert-success error'>$successMessage</div>";
    }
    ?>


<?php include_once('partial files\footer.php') ?>