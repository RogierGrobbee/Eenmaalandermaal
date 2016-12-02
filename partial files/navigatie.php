<?php
if($inputRubriekId != 0) {
    $navigatieArray = array();
    $navigatieRubriek = $huidigeRubriek;
    array_push($navigatieArray, $navigatieRubriek);

    while ($navigatieRubriek->superrubriek != null) {
        foreach ($rubriekArray as $k => $rubriek) {
            if ($navigatieRubriek->superrubriek == $rubriek->rubrieknummer) {
                array_push($navigatieArray, $rubriek);
                $navigatieRubriek = $rubriek;
            }
        }
    }

    $aantal = count($navigatieArray)-1;

    echo '<div class="navigatie">';
    while ($aantal >= 0) {
        echo '<a href=index.php?rubriek=' . $navigatieArray[$aantal]->rubrieknummer . '>' . $navigatieArray[$aantal]->rubrieknaam . '</a>';

        if ($aantal != 0) {
            echo " > ";
        }

        $aantal--;
    }
    echo "</div>";
}
?>