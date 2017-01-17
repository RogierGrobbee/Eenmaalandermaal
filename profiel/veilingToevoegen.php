<?php
include_once('..\partial files\header.php');
include_once('..\partial files\databaseconnection.php');
include_once('..\partial files\models\rubriek.php');
include_once('..\partial files\models\voorwerpinrubriek.php');

function loadJSScripts() {
    echo '<script type="text/javascript" src="../js/jquery-3.1.1.min.js"></script>';
    echo '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>';
    echo '<script type="text/javascript" src="../js/veilingToevoegen.js"></script>';
    echo '<script type="text/javascript" src="../js/addImages.js"></script>';
}


function userIsVerkoper($username)
{
    global $db;
    $statement = $db->prepare('SELECT verkoper FROM gebruiker WHERE gebruikersnaam = :gebruikersnaam');
    $statement->bindParam(':gebruikersnaam', $username);
    $statement->execute();
    $row = $statement->fetch();
    return $row['verkoper'];
}

if (empty($_SESSION['user']) || userIsVerkoper($_SESSION['user']) == 0) {
    header('Location: ../login.php');
}

$rubriekArray = loadRubrieken();
function getVoorwerpnummer($title, $username)
{
    global $db;
    $statement = $db->prepare("select top 1 voorwerpnummer from voorwerp where titel=:title and verkoper=:username order by voorwerpnummer desc");
    $statement->bindParam(':username', $username);
    $statement->bindParam(':title', $title);
    $statement->execute();
    $row = $statement->fetch();
    return $row['voorwerpnummer'];
}

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

function returnAllCountries2()
{
    global $db;
    $query = $db->query("SELECT landnaam FROM land");
    echo "<select class='form-control' name='country'>";
    foreach ($query as $row) {
        if ($row['landnaam'] == 'Nederland') {
            echo "<option selected='selected' value = " . $row['landnaam'] . " >" . $row['landnaam'] . "</option>";
        } else {
            echo "<option value = " . $row['landnaam'] . " >" . $row['landnaam'] . "</option>";
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

function showRootRubrieken() {
    $rootRubriek = getRubriekenBySuperrubriek();

    foreach ($rootRubriek as $row) {
        $id = $row->rubrieknummer;

        echo "<a href='#" . $id . "' data-id='" . $id . "' data-toggle='collapse' onclick='rubriekClick(this)' data-state='0'>"
            . $row->rubrieknaam . "</a>";
        echo "<br>";
        echo "<div id='" . $id . "' class='collapse margin-left'>";
        echo '</div>';
    }
}

$j = 0; //Variable for indexing uploaded image
$target_path = "../itemImages/"; //Declaring Path for uploaded images
$noError = false;
$itemUpload = false;
$errorMessage = "";
$successMessage = "";
if (isset($_POST['toevoegen'])) {
    if (empty($_POST['titel']) ||
        empty($_POST['beschrijving']) ||
        empty($_POST['plaatsnaam'])
    ) {
        $errorMessage = "Niet alles ingevuld.";
    } else if (!is_numeric($_POST['startprijs'])) {
        $errorMessage = "Startprijs mag alleen cijfers bevatten.";
    } else if (empty($_POST['startprijs'])) {
        $errorMessage = "Startprijs moet hoger zijn dan €0";
    } else if (!preg_match("/^[a-zA-Z]+$/", $_POST["plaatsnaam"])) {
        $errorMessage = "Plaatsnaam mag alleen letters bevatten.";
    } else if (!empty($_POST['verzendkosten']) && !is_numeric($_POST['verzendkosten'])) {
        $errorMessage = "Verzendkosten mag alleen cijfers bevatten.";
    } else {
        $titel = htmlspecialchars($_POST['titel']);
        $beschrijving = htmlspecialchars($_POST['beschrijving']);
        $plaatsnaam = htmlspecialchars($_POST['plaatsnaam']);
        if (!empty($_POST['betalingsinstructie'])) {
            $betalingsinstructie = htmlspecialchars($_POST['betalingsinstructie']);
        } else {
            $betalingsinstructie;
        }
        if (!empty($_POST['verzendinstructies'])) {
            $verzendinstructies = htmlspecialchars($_POST['verzendinstructies']);
        } else {
            $verzendinstructies;
        }
        if (!empty($_POST['verzendkosten'])) {
            $verzendkosten = $_POST['verzendkosten'];
            if ($_FILES['file']['size'][0] <= 0) {
                $noError = true;
            }
        } else {
            $verzendkosten;
            if ($_FILES['file']['size'][0] <= 0) {
                $noError = true;
            }
        }
        if ($_FILES['file']['size'][0] > 0) {
            for ($i = 0; $i < count($_FILES['file']['name']); $i++) {//loop to get individual element from the array
                $validextensions = array("jpeg", "jpg", "png");  //Extensions which are allowed
                $ext = explode('.', basename($_FILES['file']['name'][$i]));//explode file name from dot(.)
                $file_extension = end($ext); //store extensions in the variable
                $j = $j + 1;//increment the number of uploaded images according to the files in array
                if ($_FILES["file"]["size"][$i] > 1000000) {
                    $errorMessage = "Het bestand is te groot.";
                } else if (!in_array($file_extension, $validextensions)) {
                    $errorMessage = "Het bestand is niet het juiste type.";
                } else {
                    $noError = true;
                    $itemUpload = true;
                }
            }
        }
        if (!isset($_POST['rubriekenList'])) {
            $noError = false;
            $errorMessage = "De veiling moet minimaal in 1 rubriek zitten.";
        }
        if ($noError) {
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
            $stmt->bindValue(':verzendkosten', $verzendkosten, PDO::PARAM_STR);
            $stmt->bindValue(':verzendinstructies', $verzendinstructies, PDO::PARAM_STR);
            $stmt->bindValue(':verkoper', $_SESSION['user'], PDO::PARAM_INT);
            $stmt->execute();
            $stmt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $successMessage = "Veiling is toegevoegd";

            if (empty($errorMessage) &&  isset($_POST['rubriekenList'])) {
                for ($deIndex = 0; $deIndex < count($_POST['rubriekenList']); $deIndex++) {
                    $voorwerpnummer = getVoorwerpnummer($titel, $_SESSION['user']);
                    insertVoorwerpInRubriek($voorwerpnummer, $_POST['rubriekenList'][$deIndex]);
                }
            }

            if (empty($errorMessage) && $itemUpload) {
                for ($i = 0; $i < count($_FILES['file']['name']); $i++) {
                    $ext = explode('.', basename($_FILES['file']['name'][$i]));//explode file name from dot(.)
                    $file_extension = end($ext); //store extensions in the variable
                    $target_path_file = $i . "-" . date('dmy') . "-" . getVoorwerpnummer($titel, $_SESSION['user']) . "." . $ext[count($ext) - 1];
                    move_uploaded_file($_FILES['file']['tmp_name'][$i], $target_path . $target_path_file);

                    $sql = "INSERT INTO bestand (filenaam, voorwerpnummer) VALUES(:bestand, :voorwerpnummer)";
                    $stmt = $db->prepare($sql);
                    $stmt->bindValue(':bestand', $target_path_file, PDO::PARAM_STR);
                    $stmt->bindValue(':voorwerpnummer', getVoorwerpnummer($titel, $_SESSION['user']), PDO::PARAM_STR);
                    $stmt->execute();
                    $stmt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                }
            }

//            if (empty($errorMessage) && !empty($rubrieknummers)) {
//                for ($i = 0; $i < count($rubrieknummers); $i++) {
//                    $sql = " INSERT INTO voorwerpinrubriek (voorwerpnummer,rubriekoplaagsteniveau ) VALUES(:voorwerpnummer, :rubrieknummer)";
//                    $stmt = $db->prepare($sql);
//                    $stmt->bindValue(':voorwerpnummer', getVoorwerpnummer($titel, $_SESSION['user']), PDO::PARAM_STR);
//                    $stmt->bindValue(':rubrieknummer', $rubrieknummers[$i], PDO::PARAM_STR);
//                    $stmt->execute();
//                    $stmt->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//                }
//            }
        }
    }
}
    ?>
    <h1>Profiel</h1>

    <?php include_once('..\partial files\sidebar.php');
        loadProfileSidebar($_SESSION[user], 5);
    ?>
    <div class="col-sm-12">
        <h3>Veiling Toevoegen</h3>
    </div>
    <form method="post" enctype="multipart/form-data" id="add-veiling-form" name="add-veiling-form">
        <row>
            <div class="form-group">
                <table class="registration-table">
                    <tr>
                        <td>Titel</td>
                        <td><input class="form-control" maxlength="100" value="<?php if (isset($_POST['titel'])) {
                                echo $_POST['titel'];
                            } ?>" type="text" name="titel"></td>
                    </tr>
                    <tr>
                        <td>Beschrijving</td>
                        <td>
                        <textarea class="form-control" maxlength="8000" rows="5"
                                  name="beschrijving"><?php if (isset($_POST['beschrijving'])) {
                                echo $_POST['beschrijving'];
                            } ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>Startprijs</td>
                        <td><input class="form-control" value="<?php if (isset($_POST['startprijs'])) {
                                echo $_POST['startprijs'];
                            } ?>" type="text" name="startprijs"></td>
                    </tr>
                    <tr>
                        <td>Betalingswijze</td>
                        <td>
                            <?php echo returnPaymentMethode(); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Betalingsinstructie</td>
                        <td><input class="form-control" maxlength="255"
                                   value="<?php if (isset($_POST['betalingsinstructie'])) {
                                       echo $_POST['betalingsinstructie'];
                                   } ?>" type="text" name="betalingsinstructie"></td>
                    </tr>
                    <tr>
                        <td>Plaatsnaam</td>
                        <td><input class="form-control" maxlength="30" value="<?php if (isset($_POST['plaatsnaam'])) {
                                echo $_POST['plaatsnaam'];
                            } ?>" type="text" name="plaatsnaam"></td>
                    </tr>
                    <tr>
                        <td>Looptijd</td>
                        <td>
                            <?php echo returnDuration(); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Land</td>
                        <td>
                            <?php echo returnAllCountries2(); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Verzendkosten</td>
                        <td><input class="form-control" maxlength="5" value="<?php if (isset($_POST['verzendkosten'])) {
                                echo $_POST['verzendkosten'];
                            } ?>" type="text" name="verzendkosten"></td>
                    </tr>
                    <tr>
                        <td>Verzendinstructies</td>
                        <td><input class="form-control" maxlength="255"
                                   value="<?php if (isset($_POST['verzendinstructies'])) {
                                       echo $_POST['verzendinstructies'];
                                   } ?>" type="text" name="verzendinstructies"></td>
                    </tr>
                    <tr>
                        <td>
                            Afbeelding toevoegen
                        </td>
                        <td>
                            Alleen JPEG,PNG en JPG zijn toegestaan. De maximale bestand grote is 1MB.
                            <div><input name="file[]" type="file" id="file"/></div>
                            <br/>
                            <input type="button" id="add_more" value="Voeg meer bestanden toe">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Rubriek Toevoegen
                        </td>
                        <td>
                            <!-- Trigger the modal with a button -->
                                <select selected='selected' name="rubriekenList[]" id="rubrieken-list" multiple="multiple" form="add-veiling-form">

                                </select>
                                <button type="button" class="btn rubriek-button" data-toggle="modal" data-target="#myModal">Voeg rubriek toe</button>
                                <button type="button" class="btn rubriek-button" id="delete-rubriek" disabled>Verwijder rubriek</button>
                        </td>
                        <!-- Modal -->
                        <div class="modal fade" id="myModal" role="dialog">
                            <div class="modal-dialog">

                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Rubrieken</h4>
                                    </div>
                                    <div class="modal-body">
                                        <?php showRootRubrieken(); ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </tr>
                </table>
            </div>
        </row>
        <row>
            <div class="col-sm-12 submit-registrion orangeButton">
                <input type="submit" name="toevoegen" value="Toevoegen">
            </div>
        </row>
    </form>
    <br><br>

    <row>
        <div class="row" style="margin-top: -22.5px;">
            <br>
            <?php
            if (!empty($errorMessage)) {
                echo "<div class='alert alert-danger error'>$errorMessage</div>";
            } else if (!empty($successMessage)) {
                echo "<div class='alert alert-success error'>$successMessage</div>";
            }
            ?>
    </row>


    <?php include_once('..\partial files\footer.php'); ?>