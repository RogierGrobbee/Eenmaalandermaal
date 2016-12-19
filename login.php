<?php include_once('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();
include_once('partial files\header.php'); ?>

    <h1>Log In</h1>

<?php include_once('partial files\sidebar.php');
loadSidebar($rubriekArray, null);

$errorMessage;
if (!empty($_POST['wachtwoord'])) {
    $password = $_POST['wachtwoord'];

    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number = preg_match('@[0-9]@', $password);
}
if (isset($_POST['Login'])) {
    if (
        empty($_POST['gebruikersnaam']) ||
        empty($_POST['wachtwoord'])
    ) {
        $errorMessage = "Niet alles is ingevuld.";
    } else
        if(getValidation($_POST['gebruikersnaam'])) {
            $password = $_POST['wachtwoord'];
            $username = $_POST['gebruikersnaam'];
            $hash = getPassword($username);

            if (password_verify($password, $hash)) {
                $_SESSION['user'] = $username;
                header('Location: index.php');
            } else {
                $errorMessage = 'Combinatie gebruikersnaam en wachtwoord zijn onjuist.';
            }
        }
        else{
            $errorMessage = 'Combinatie gebruikersnaam en wachtwoord zijn onjuist.';
        }
}

?>

    <div class="row" style="margin-top: -22.5px;">
            <br>
            <?php
            if (!empty($errorMessage)) {
                echo "<div class='alert alert-danger error'>";
                echo $errorMessage;
                echo "</div>";
            }
            ?>
    <form method="post">
        <div class="col-xs-12 registration-link">Om mee te kunnen doen aan de veilingen moet u inloggen. Als u nog geen account heeft,
            <a href="registreer.php">druk dan hier om een account te registreren.</a> </div>

        <div class="col-sm-6 col-xs-8">
            <table class="registration-table">
                <tr>
                    <td>Gebruikersnaam</td>
                    <td><input pattern="[a-zA-Z0-9-]+" value="<?php if(isset($_POST['gebruikersnaam'])){ echo $_POST['gebruikersnaam'];}?>" type="text" name="gebruikersnaam" ></td>
                </tr>
                <tr>
                    <td>Wachtwoord</td>
                    <td><input type="password" name="wachtwoord" ></td>
                </tr>
            </table>
        </div>
        <div class="col-sm-3 col-xs-3 submit-registration">
            <button type="submit" name="Login" value="Login">Login</button>
        </div>
    </form>
        <form action="vergeten.php" method="post">
            <button type="submit" class="left" name="Wachtwoord Vergeten" value="Wachtwoord Vergeten?">Wachtwoord vergeten?</button>
        </form>
    </div>

<?php include_once('partial files\footer.php') ?>