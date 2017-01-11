<?php
require_once('models/verkoper.php');
require_once('models/rubriek.php');

function loadProfileSidebar($username, $selectedTab)
{
    $verkoper = getVerkoperByUsername($username);

    echo '<div class="list-group col-sm-3">';
    echo "<ul class='topnav' id='myTopnav''>";
    echo "<li><strong><div class='list-group-item rubriekList' href='#'>Menu</div></strong></li>";
    if ($selectedTab == 1) {
        echo '<li><a class="list-group-item active" href=overzicht.php>Overzicht</a></li>';
    } else {
        echo '<li><a class="list-group-item" href=overzicht.php>Overzicht</a></li>';
    }
    if ($selectedTab == 2) {
        echo '<li><a class="list-group-item active" href=overzicht.php#>Wachtwoord wijzigen</a></li>';
    } else {
        echo '<li><a class="list-group-item" href=overzicht.php#>Wachtwoord wijzigen</a></li>';
    }
    if ($selectedTab == 3){
        echo '<li><a class="list-group-item active" href=biedingen.php>Bod historie</a></li>';
    }else{
        echo '<li><a class="list-group-item" href=biedingen.php>Bod historie</a></li>';
    }

    if ($verkoper == null){
        if ($selectedTab == 4){
            echo '<li><a class="list-group-item active" href=verkoop-validation.php>Verkoper worden</a></li>';
        }else{
            echo '<li><a class="list-group-item" href=verkoop-validation.php>Verkoper worden</a></li>';
        }
    }
    else if (!$verkoper->verkoper){
        if ($selectedTab == 4){
            echo '<li><a class="list-group-item active" href=validation-page.php>Verkopersaccount verifiëren</a></li>';
        }else{
            echo '<li><a class="list-group-item" href=validation-page.php>Verkopersaccount verifiëren</a></li>';
        }
    }
    else{
        if ($selectedTab == 4){
            echo '<li><a class="list-group-item active" href=#>Mijn veilingen</a></li>';
        }else {
            echo '<li><a class="list-group-item" href=#>Mijn veilingen</a></li>';
        }
    }

    echo "<li class='icon'>";
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





