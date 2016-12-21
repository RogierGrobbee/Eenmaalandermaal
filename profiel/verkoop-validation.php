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
<form method="post">
<div class="col-sm-6">
    <table class="registration-table">
        <tr>
            <td>Banknummer: </td>
            <td><input maxlength="255"  value="<?php if(isset($_POST['banknummer'])){ echo $_POST['banknummer'];}?>" type="text" name="banknummer" ></td>
        </tr>
        <tr>
            <td>Creditcardnummer: </td>
            <td><input maxlength="255" value="<?php if(isset($_POST['creditcardnummer'])){ echo $_POST['email'];}?>" type="text" name="creditcardnummer" ></td>
        </tr>

    </table>
</row>
    <row>
        <div class="col-sm-12">
            <input  type="submit" name="registreer" value="Registreren">
        </div>

    </row>
    </form>




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
