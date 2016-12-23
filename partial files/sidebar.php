<?php

require_once('models/rubriek.php');

function loadProfileSidebar()
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

function loadRubriekenSidebar($selectedRubriek)
{
    $rubriekArray = loadAllRubrieken();

    echo "<div class='list-group col-sm-3'>
            <ul class='topnav' id='myTopnav'>
          <li><strong><div class='list-group-item rubriekList' href='#'>Rubrieken</div></strong></li>";
    foreach ($rubriekArray as $k => $rubriek) {
        if ($rubriek->superrubriek == null) {
            $listItemClass = "list-group-item";
            if ($rubriek == $selectedRubriek) {
                $listItemClass = "list-group-item active";
            }

            echo '<li><a class="' . $listItemClass . '" href="rubriek.php?rubriek=' . $rubriek->rubrieknummer . '#">' .
                $rubriek->rubrieknaam . '</a></li>';


        }
    }
    echo "<li class='icon'>
    <a href='javascript:void(0);' onclick='myFunction()'>&#9776;</a>
    </li></ul>
    </div>
    <div class='col-sm-9'>";
}
?>





