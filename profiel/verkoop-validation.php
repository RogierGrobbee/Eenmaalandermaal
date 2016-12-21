<?php

include_once('..\partial files\header.php'); ?>

<h1>Profiel</h1>
<?php
loadSidebar();
if (isset($_SESSION["user"])) {
    $username = $_SESSION["user"];
}
$username = 'jasper';

include_once ('..\partial files\databaseconnection.php');
$user = getUserByUsername($username);

?>
<row>
    <?php

    function isUserSeller($user)
    {
        global $db;
        $statement = $db->prepare("SELECT verkoper FROM gebruiker WHERE gebruikersnaam = :username ");
        $statement->execute(array(':username' => $user));
        $row = $statement->fetch();
       return $row['verkoper'];
    }



    $errorString = "";

    if (isset($_POST['registratieverkoop'])) {
        if (empty($_POST['banknummer']) &&
            empty($_POST['creditcardnummer'])
        ) {
            $errorString = "IBAN <strong>of</strong> creditcardnummer moet ingevuld zijn.";
        } else {
            if (empty($_POST['banknummer'])) {
                $controleoptie = 'Creditcard';
            } else {
                $controleoptie = 'Bank';
            }

            global $db;
            $sql = "INSERT INTO verkoper (gebruikersnaam, bankrekening, creditcard, controleoptie) VALUES
                (:gebruiker, :bankrekening, :creditcard, :controleoptie)";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':gebruiker', $_SESSION['user'], PDO::PARAM_STR);
            $stmt->bindValue(':bankrekening', $_POST['banknummer'], PDO::PARAM_STR);
            $stmt->bindValue(':creditcard', $_POST['creditcardnummer'], PDO::PARAM_STR);
            $stmt->bindValue(':controleoptie', $controleoptie, PDO::PARAM_STR);
            $stmt->execute();
        }
    }

    ?>

    <h3>Vul uw bank of creditcardnummer in.</h3>
    Het is verplicht om één van de twee in te vullen.
    Bij het invullen van een IBAN-nummer én een creditcardnummer zal het IBAN-nummer ter verificatie worden gebruikt.
    <br><br>

    <form method="POST" class="form-horizontal">
        <div class="form-group">
            <label class="control-label col-sm-2" >IBAN:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control"  name="banknummer" >
            </div>
        </div>
    <div class="form-group">
        <label class="control-label col-sm-2" >Creditcardnummer:</label>
        <div class="col-sm-10">
            <input type="text" class="form-control"  name="creditcardnummer" >
        </div>
    </div>


<row>
    <div >
        <input  type="submit" name="registratieverkoop" value="Meld mij aan als verkoper">
    </div>
    </form>
    <br><br>
    <?php if (empty($errorString)) {

    } else { ?>
        <div class="alert alert-danger error">
            <?php echo $errorString; ?>
        </div>
    <?php } ?>
</row>




<?php
include_once('..\partial files\footer.php');

function loadSidebar()
{
    echo '<div class="list-group col-sm-3">';
    echo "<ul class='topnav' id='myTopnav''>";
    echo "<li><strong><div class='list-group-item rubriekList' href='#'>Rubrieken</div></strong></li>";

    echo '<li><a class="list-group-item active" href=overzicht.php#>Overzicht</a></li>';
    echo '<li><a class="list-group-item" href=overzicht.php#>Wachtwoord wijzigen</a></li>';
    echo '<li><a class="list-group-item" href=overzicht.php#>Verkoper worden</a></li>';

    echo "<li class='icon''>";
    echo "<a href='javascript:void(0);' onclick='myFunction()''>&#9776;</a>";
    echo "</li>";
    echo "</ul>";
    echo '</div><div class="col-sm-9">';
}

?>
