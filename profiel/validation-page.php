<?php

include_once('..\partial files\header.php'); ?>

<h1>Profiel</h1>
<?php
loadSidebar();

include_once ('..\partial files\databaseconnection.php');
$user = getUserByUsername($username);

function calculateSellerExpire($code)
{
    global $db;
    $statement = $db->prepare("select datumTijd from verkoper where validatiecode = :validatiecode");
    $statement->execute(array(':validatiecode' => $code));
    $row = $statement->fetch();

    $expire = date("Y-m-d H:i:s", strtotime('+0 hour'));
    $timestamp1 = strtotime($expire);
    $timestamp2 = strtotime($row['datumTijd']);
    $hour = abs($timestamp2 - $timestamp1) / (60 * 60);
    if ($hour > 168) {
        return false;
    } else {
        return true;
    }
}


function doesSellerValidationCodeexist($code)
{
    global $db;
    $statement = $db->prepare("SELECT validatiecode FROM verkoper WHERE validatiecode = :code");
    $statement->execute(array(':code' => $code));
    $row = $statement->fetch();
    if (!$row) {
        return false;
    } else {
        return true;
    }

}

if (isset($_POST['valideer'])) {
    if (empty($_POST['validatiecode'])) {
        $messageString = "<div class='alert alert-danger error'>Er is niks ingevuld.</div>";
    } else if (!doesSellerValidationCodeexist($_POST['validatiecode'])) {
        $messageString = "<div class='alert alert-danger error'>Foutieve code.</div>";
    } else if (!calculateSellerExpire($_POST['validatiecode'])) {
        $messageString = "<div class='alert alert-danger error'>Code is verlopen.</div>";
    } else {
        validateSeller($_POST['validatiecode']);
        $messageString = "<div class='alert alert-success error'>U bent nu verkoper.</div>";
    }
}

function validateSeller($code)
{
    global $db;
    $sth = "UPDATE g
            SET g.verkoper = 1
            FROM gebruiker AS g
            INNER JOIN verkoper AS v
            ON g.gebruikersnaam = v.gebruikersnaam
            WHERE v.validatiecode  = :validatie";
    $sthm = $db->prepare($sth);
    $sthm->bindParam(':validatie', $code);
    $sthm->execute();
}

?>



<h3>Valideer hier uw verkopersaccount.</h3>
Vul de code in die u via de post heeft ontvangen.
<br><br>

<form method="POST" class="form-horizontal">
    <div class="form-group">
        <label class="control-label col-sm-2" >Validatiecode:</label>
        <div class="col-sm-10">
            <input type="text" class="form-control"  name="validatiecode" >
        </div>
    </div>
    <row>
        <div >
            <input  type="submit" name="valideer" value="Valideer">
        </div>
</form>
<br><br>
<?php if (empty($messageString)) {

} else { ?>

    <?php echo $messageString; ?>
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
