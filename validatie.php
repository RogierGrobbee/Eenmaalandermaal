<?php
require('partial files\models\validation.php');
require('partial files\models\rubriek.php');
require('partial files\models\gebruiker.php');


$errorString = "";


if (isset($_POST['valideer'])) {
    $code = $_POST['validatiecode'];
    if (calculateExpire($code) == 1 && doesValidationCodeexist($code) == 1) {
        validateUser($code);
        session_start();
        $_SESSION['message'] = "Goedgekeurd, u kunt nu inloggen.";
        header('Location: login.php');

    } else {
        $errorString =  "<div class='alert alert-danger'>Validatiecode niet correct of is verlopen.</div>";

    }
}

function calculateExpire($code)
{
    $datumtijd = getDatumTijdByValicationCode($code);

    $expire = date("Y-m-d H:i:s", strtotime('+0 hour'));
    $timestamp1 = strtotime($expire);
    $timestamp2 = strtotime($datumtijd->datumTijd);
    $hour = abs($timestamp2 - $timestamp1)/(60*60);
    if ($hour > 4 ) {
        return false;
    } else {
        return true;
    }


}

$rubriekArray = loadAllRubrieken();
include_once('partial files\header.php');
?>
    <h1>Registreer</h1>

<?php include_once('partial files\sidebar.php');
loadRubriekenSidebar(null);
?>
    <row>
        <div class="col-sm-12">
            <?php echo $errorString; ?>

            <h3>E-mailadres bevestigen</h3>
            <p> Uw e-mailadres moet bevestigd worden voor dat u kan inloggen.<br>
                Vul hier de naar uw e-mailadres gestuurde validatiecode in:
            </p>
            <form method="post" >
                Validatiecode: <input type="text" name="validatiecode">
                <input type="submit" name="valideer" value="Valideer">
            </form>
        </div>
    </row>


<?php include_once('partial files\footer.php') ?>