<?php include_once('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();
include_once('partial files\header.php');
cantVisitLoggedIn();
?>

    <h1>Veiling Toevoegen</h1>

<?php include_once('partial files\sidebar.php');
?>
*/select titel, beschrijving, startprijs, betalingswijze, betalingsinstructie,plaatsnaam,land,looptijd, verzendkosten
    ,verzendinstructies, verkoper,
/*

    <form method="post">
        <row>
            <div class="col-sm-6">
                <table class="registration-table">
                    <tr>
                        <td>Titel</td>
                        <td><input maxlength="255" placeholder="name@example.com" value="<?php if(isset($_POST['email'])){ echo $_POST['email'];}?>" type="text" name="email" ></td>
                    </tr>
                    <tr>
                        <td>Beschrijving</td>
                        <td><input maxlength="35" value="<?php if(isset($_POST['gebruikersnaam'])){ echo $_POST['gebruikersnaam'];}?>" type="text" name="gebruikersnaam" ></td>
                    </tr>
                    <tr>
                        <td>Startprijs</td>
                        <td><input value="<?php if(isset($_POST['voornaam'])){ echo $_POST['voornaam'];}?>" type="text" name="voornaam" ></td>
                    </tr>
                    <tr>
                        <td>Betalingswijze</td>
                        <td><input  value="<?php if(isset($_POST['achternaam'])){ echo $_POST['achternaam'];}?>" type="text" name="achternaam" ></td>
                    </tr>
                    <tr>
                        <td>Betalingsinstructie</td>
                        <td><input maxlength="100" type="password" name="wachtwoord" ></td>
                    </tr>
                    <tr>
                        <td>Plaatsnaam</td>
                        <td><input maxlength="100" type="password" name="wachtwoord2" ></td>
                    </tr>
                    <tr>
                        <td>Looptijd</td>
                        <td><input  placeholder="jjjj-mm-dd" value="<?php if(isset($_POST['geboortedatum'])){ echo $_POST['geboortedatum'];}?>" name="geboortedatum" type="date" data-date-inline-picker="true"/></td>
                    </tr>

                </table>
            </div>
            <div class="col-sm-6">
                <table class="registration-table">
                    <tr>
                        <td>Land</td>
                        <td>
                            <?php echo returnAllCountries(); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>verzendkosten</td>
                        <td><input maxlength="53" value="<?php if(isset($_POST['adres'])){ echo $_POST['adres'];}?>" type="text" name="adres" ></td>
                    </tr>
                    <tr>
                        <td>Verzendinstructies</td>
                        <td><input maxlength="100" value="<?php if(isset($_POST['plaats'])){ echo $_POST['plaats'];}?>" type="text" name="plaats" ></td>
                    </tr>
                    <tr>
                        <td>Rubriek</td>
                        <td><input  placeholder="1234AB" value="<?php if(isset($_POST['postcode'])){ echo $_POST['postcode'];}?>" type="text" name="postcode" ></td>
                    </tr>
                    <tr>
                        <td>Afbeelding</td>
                        <td><input maxlength="15" value="<?php if(isset($_POST['telefoon1'])){ echo $_POST['telefoon1'];}?>" type="text" name="telefoon1"></td>
                    </tr>
                    <tr>
                        <td>Geheime vraag</td>
                        <td>
                            <?php echo returnGeheimeVragen(); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Antwoord</td>
                        <td><input maxlength="100" value="<?php if(isset($_POST['antwoord'])){ echo $_POST['antwoord'];}?>" type="text" name="antwoord"></td>
                    </tr>

                </table>
            </div>
        </row>

        <row>
            <div class="col-sm-12 submit-registrion orangeButton">
                <input  type="submit" name="registreer" value="Registreren">
            </div>

        </row>
    </form>
<?php include_once('partial files\footer.php') ?>