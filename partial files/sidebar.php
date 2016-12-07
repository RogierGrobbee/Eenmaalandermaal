<?php
function loadSidebar($rubriekArray, $selectedRubriek)
{
    echo '<div class="list-group col-sm-3">';
    foreach ($rubriekArray as $k => $rubriek) {
        if ($rubriek->superrubriek == null) {
            $listItemClass = "list-group-item";
            if ($rubriek == $selectedRubriek) {
                $listItemClass = "list-group-item active";
            }
            echo '<a class="' . $listItemClass . '"href=rubriek.php?rubriek=' . $rubriek->rubrieknummer . '#>' . $rubriek->rubrieknaam . '</a>';
        }
    }
    echo '</div><div class="col-sm-9">';
}

?>

