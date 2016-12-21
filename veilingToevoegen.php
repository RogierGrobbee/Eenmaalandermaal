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
                            <textarea class="form-control" maxlength="8000" rows="5" id="beschrijving"></textarea>
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
                        <td>verzendkosten</td>
                        <td><input class="form-control" maxlength="5" value="<?php if(isset($_POST['adres'])){ echo $_POST['adres'];}?>" type="text" name="adres" ></td>
                    </tr>
                    <tr>
                        <td>Verzendinstructies</td>
                        <td><input class="form-control" maxlength="255" value="<?php if(isset($_POST['plaats'])){ echo $_POST['plaats'];}?>" type="text" name="plaats" ></td>
                    </tr>
                    <tr>
                        <td>Afbeelding</td>
                        <td><input class="form-control" maxlength="15" value="<?php if(isset($_POST['telefoon1'])){ echo $_POST['telefoon1'];}?>" type="text" name="telefoon1"></td>
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