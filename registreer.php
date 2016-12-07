<?php include_once('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();
include_once('partial files\header.php'); ?>

    <h1>Registreer</h1>

<?php include_once('partial files\sidebar.php');
loadSidebar($rubriekArray, null); ?>
    <div class="col-sm-9">
        <row>
            <div class="col-sm-6">
                <table class="registration-page-table">
                    <tr>
                        <td>Email</td>
                        <td><input type="text" name="email"></td>
                    </tr>
                    <tr>
                        <td>Voornaam</td>
                        <td><input type="text" name="voornaam"></td>
                    </tr>
                    <tr>
                        <td>Achternaam</td>
                        <td><input type="text" name="achternaam"></td>
                    </tr>
                    <tr>
                        <td>Wachtwoord</td>
                        <td><input type="text" name="wachtwoord"></td>
                    </tr>
                    <tr>
                        <td>Wachtwoord 2</td>
                        <td><input type="text" name="wachtwoord2"></td>
                    </tr>
                    <tr>
                        <td>Geboortedatum</td>
                        <td><input type="text" name="geboortedatum"></td>
                    </tr>
                    <tr>
                        <td>Geboortedatum</td>
                        <td><input type="text" name="geboortedatum"></td>
                    </tr>
                    <tr>
                        <td></td><td></td>
                    </tr>
                    <tr>
                        <td>Geheime vraag</td>
                        <td>
                            <select class="Registration-page-input">
                                <option value="1">Geheime vraag 1</option>
                                <option value="2">Geheime vraag 2</option>
                                <option value="3">Geheime vraag 3</option>
                                <option value="4">Geheime vraag 4</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Antwoord</td>
                        <td><input type="text" name="antwoord"></td>
                    </tr>


                </table>

            </div>
            <div class="col-sm-6">
                <table class="registration-page-table">
                    <tr>
                        <td>Adres</td>
                        <td><input type="text" name="adres"></td>
                    </tr>
                    <tr>
                        <td>Plaats</td>
                        <td><input type="text" name="plaats"></td>
                    </tr>
                    <tr>
                        <td>Postcode</td>
                        <td><input type="text" name="postcode"></td>
                    </tr>
                    <tr>
                        <td>Telefoon 1</td>
                        <td><input type="text" name="telefoon"></td>
                    </tr>
                    <tr>
                        <td>Telefoon 2</td>
                        <td><input type="text" name="telefoone"></td>
                    </tr>
                </table>
            </div>
            <div class=""></div>


            <div class="col-sm-12">
                <input class="registration-button" type="button" value="Registreren">
                <input class="registration-button" type="button" value="Annuleren">
            </div>
        </row>

    </div>
<?php include_once('partial files\footer.php') ?>