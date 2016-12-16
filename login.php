<?php include_once('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();
include_once('partial files\header.php'); ?>

    <h1>Log In</h1>

<?php include_once('partial files\sidebar.php');
loadSidebar($rubriekArray, null);

$errorMessage = "";
$successMessage = "";
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
        $errorMessage = "Niet alles ingevuld.";
    } else
        if (preg_match('/\s/',$_POST['gebruikersnaam'])) {
            $errorMessage = "Gebruikersnaam mag geen spaties bevatten.";
        } else if (!$uppercase || !$lowercase || !$number || strlen($password) < 8) {
            $errorMessage = "Wachtwoord moet minimaal 8 character lang zijn en 1 kleine letter, 1 hoofdletter en een nummer bevatten.";
        } else {
            $password = $_POST['wachtwoord'];
            $username= $_POST['gebruikersnaam'];
            $hash = getPassword($username);
            if (password_verify($password,$hash)) {
                $_SESSION['login']=true;
                $_SESSION['user'] = $username;
                echo 'Login is oke';
                // header('Location: index.php');
            } else {
                echo 'Combinatie gebruikersnaam en wachtwoord zijn onjuist';
            }
        }
}

?>


    <form method="post">
        <row>
            <div class="col-sm-6">
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
        </row>
        <row>
            <div class="col-sm-12 submit-registrion">
                <input type="submit" name="Login" value="Login">
            </div>

        </row>
    </form>
    <br><br>
    <row>
        <div class="col-sm-12">
            <br>
            <input type="submit" name="Wachtwoord Vergeten" value="Wachtwoord Vergeten?">
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