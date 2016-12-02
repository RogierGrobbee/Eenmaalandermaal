<?php
function loadSubrubrieken($rubriekArray, $selectedRubriek)
{
    $hasSubrubriek = false;
    foreach ($rubriekArray as $k => $rubriek) {
        if ($rubriek->superrubriek == $selectedRubriek->rubrieknummer && $rubriek->superrubriek != null) {
            $hasSubrubriek = true;
        }
    }
    if ($hasSubrubriek) {
        echo '<div class="row subrubrieken">
            <h2>Subrubrieken</h2>';
        foreach ($rubriekArray as $k => $rubriek) {
            if ($rubriek->superrubriek == $selectedRubriek->rubrieknummer && $rubriek->superrubriek != null) {
                echo '<div class="col-xs-6 col-md-4 col-lg-4">
                    <a href=index.php?rubriek=' . $rubriek->rubrieknummer . '#>' . $rubriek->rubrieknaam . '</a>
                </div>';
            }
        }
        echo '</div>';
    }
}
?>
