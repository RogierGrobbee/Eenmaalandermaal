<?php
function loadSidebar($rubriekArray, $selectedRubriek)
{
    echo '<div class="list-group col-sm-3">';
    echo "<ul class='topnav' id='myTopnav''>";
    echo "<li><strong><a class=list-group-item href='#'>Rubrieken</a></strong></li>";
    foreach ($rubriekArray as $k => $rubriek) {
        if ($rubriek->superrubriek == null) {
            $listItemClass = "list-group-item";
            if ($rubriek == $selectedRubriek) {
                $listItemClass = "list-group-item active";
            }

            echo '<li><a class="' . $listItemClass . '"href=rubriek.php?rubriek=' . $rubriek->rubrieknummer . '#>' . $rubriek->rubrieknaam . '</a></li>';


        }
    }
    echo "<li class='icon''>";
    echo "<a href='javascript:void(0);' onclick='myFunction()''>&#9776;</a>";
    echo "</li>";
    echo "</ul>";
    echo '</div><div class="col-sm-9">';
}

?>





