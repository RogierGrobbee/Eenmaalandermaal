<?php
include_once('..\partial files\header.php');
include_once('..\partial files\models\gebruiker.php');
include_once('..\partial files\models\voorwerp.php');
include_once('..\partial files\models\bestand.php');
include_once('..\partial files\models\bod.php');
include_once('..\partial files\models\miscellaneous.php');
include_once('..\partial files\sidebar.php');

$username = "";
if (isset($_SESSION["user"])) {
    $username = $_SESSION["user"];
}

if ($username == "") {
    header('Location: ../login.php');
}

$errorMessage;
$succesMessage;

if (!empty($_POST['nieuw_wachtwoord'])) {
    $password = $_POST['nieuw_wachtwoord'];

    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number = preg_match('@[0-9]@', $password);
}


if (isset($_POST['Login'])) {
    if (
        empty($_POST['herhaal_wachtwoord']) ||
        empty($_POST['nieuw_wachtwoord']) ||
        empty($_POST['oud_wachtwoord'])
    ) {
        $errorMessage = "Niet alles is ingevuld.";
    } else if (!$uppercase || !$lowercase || !$number || strlen($password) < 8) {
        $errorMessage = "Wachtwoord moet minimaal 8 characters lang zijn en 1 kleine letter, 1 hoofdletter en een nummer bevatten.";
    } else if ($_POST['nieuw_wachtwoord'] != $_POST['herhaal_wachtwoord']) {
        $errorMessage = "Wachtwoorden zijn niet gelijk";
    } else if (getValidation($username)) {
        $hash = getPassword($username);

        if (password_verify($_POST['oud_wachtwoord'], $hash)) {

            $passHash = hashPass($password);
            updateWachtwoord($username, $passHash);
            $succesMessage = 'Wachtwoord is gewijzigd';
        } else {
            $errorMessage = 'Oude wachtwoord is niet correct';
        }
    } else {
        $errorMessage = 'Combinatie gebruikersnaam en wachtwoord zijn onjuist.';
    }
}

?>

<h1>Profiel</h1>
<?php loadProfileSidebar($username, 2) ?>


<div class="col-sm-12">
    <br>
    <?php
    if (!empty($errorMessage)) {
        echo "<div class='alert alert-danger error'>$errorMessage</div>";
    } else if (!empty($succesMessage)) {
        echo "<div class='alert alert-success error'>" . $succesMessage . "</div>";
    }
    ?>
    <form method="post">

        <div class="col-xs-12">
            <table class="registration-table" style="width: 60%;">
                <tr>
                    <td>Oud wachtwoord</td>
                    <td><input type="password" name="oud_wachtwoord"></td>
                </tr>
                <tr>
                    <td>Nieuw wachtwoord</td>
                    <td><input type="password" name="nieuw_wachtwoord"></td>
                </tr>
                <tr>
                    <td>Herhaal wachtwoord</td>
                    <td><input type="password" name="herhaal_wachtwoord"></td>
                </tr>
            </table>
        </div>
        <div class="col-xs-3 col-sm-1 submit-registration">
            <button type="submit" name="Login" value="Login">Wijzig wachtwoord</button>
        </div>
    </form>
</div>

<?php include_once('..\partial files\footer.php');


function CheckPassword($password)
{

}


?>
