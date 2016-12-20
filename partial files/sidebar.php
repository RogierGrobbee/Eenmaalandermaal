<?php
function loadSidebar($rubriekArray, $selectedRubriek)
{
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





