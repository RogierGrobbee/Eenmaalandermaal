<?php include_once('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();
include_once('partial files\header.php');
?>
    <h1>Registreer</h1>

<?php include_once('partial files\sidebar.php');
loadSidebar($rubriekArray, null);
$errorString = "";


if (isset($_POST['valideer'])) {
    $code = $_POST['validatiecode'];
    if (calculateExpire($code) == 1 && doesValidationCodeexist($code) == 1) {
        validateUser($code);
        $errorString =  "Goedgekeurd, u kunt nu inloggen.";

    } else {
        $errorString =  "Validatiecode niet correct of is verlopen.";

    }
}
?>
    <row>
        <div class="col-sm-12">
            <h3>E-mailadres bevestigen</h3>
            <p> Uw e-mailadres moet bevestigd worden voor dat u kan inloggen.<br>
                Vul hier de naar uw e-mailadres gestuurde validatiecode in:
            </p>
            <form method="post">
                Validatiecode: <input type="text" name="validatiecode">
                <input type="submit" name="valideer" value="Valideer">
            </form>
            <strong>
                <br>
                <?php echo $errorString; ?>
            </strong>
        </div>
    </row>


<?php include_once('partial files\footer.php') ?>