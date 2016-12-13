<?php include_once('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();
include_once('partial files\header.php');
?>
    <h1>Registreer</h1>

<?php include_once('partial files\sidebar.php');
loadSidebar($rubriekArray, null);

if (isset($_POST['valideer'])) {
    $code = $_POST['validatiecode'];
    if (doesValidationCodeexist($code)) {

        echo "Goedgekeurd, u kunt nu inloggen.";
    } else {
        echo "Validatiecode niet correct.";
    }
}
?>
    <row>
        <div class="col-sm-12">
            <h3>Vul hier uw validatiecode in:</h3>
            <form method="post">
                Validatiecode: <input type="text" name="validatiecode">
                <input type="submit" name="valideer" value="Valideer">
            </form>
            <br>
            <br>
        </div>
    </row>


<?php include_once('partial files\footer.php') ?>