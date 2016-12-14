<?php include_once('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();
include_once('partial files\header.php'); ?>

    <h1>Log In</h1>

<?php include_once('partial files\sidebar.php');
loadSidebar($rubriekArray, null);
$errorMessage = "";
$successMessage = "";
?>


    <form method="post">
        <row>
            <div class="col-sm-6">
                <table class="registration-table">
                    <tr>
                        <td>Gebruikersnaam*</td>
                        <td><input  value="<?php if(isset($_POST['gebruikersnaam'])){ echo $_POST['gebruikersnaam'];}?>" type="text" name="gebruikersnaam" ></td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-6">
                <table class="registration-table">
                    <tr>
                        <td>Wachtwoord*</td>
                        <td><input type="password" name="wachtwoord" ></td>
                    </tr>
                </table>
            </div>
        </row>

        <row>
            <div class="col-sm-12 submit-registrion">
                <input type="submit" name="registreer" value="Registreren">
            </div>

        </row>
    </form>
    <br><br>
    <row>
        <div class="col-sm-12">
            <br>
            <i>* Verplicht</i>

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