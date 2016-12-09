<?php
function loadJSScripts() {
    echo '<script type="text/javascript" src="js/countdown.js"></script>';
}

require('partial files\databaseconnection.php');
require('partial files\header.php');
?>

<h1>Meer dan 2000 veilingen! Bied nu!</h1>

<?php
//sidebar maken op basis van rubrieken
require('partial files\sidebar.php');
$rubriekArray = loadRubrieken();
loadSidebar($rubriekArray, null);
?>
    <?php
        $voorwerp = featuredVoorwerp();
        $list = loadbestanden($voorwerp->voorwerpnummer);
        $image = $list != null ? $list[0] : "NoImageAvalible.jpg";
        echo '<a href="veiling.php?voorwerpnummer='.$voorwerp->voorwerpnummer.'">
        <img src="./bestanden/'.$image.'" alt="homepage featured" class="homepage-featured-img">';

        echo '<div class="col-lg-4 col-md-5 col-sm-7 col-xs-11 homepage-featured-detail">
            <h2>'. $voorwerp->titel .'</h2>
            <div class="homepage-featured-prijs">€'. $voorwerp->startprijs .'<br>
                <span data-tijd="'. $voorwerp->looptijdeindeveiling .'" class="tijd"></span>
            </div>
            <button class="veiling-detail btn-homepage">Bied</button></div></a>' ?>


        <h1>Meest populaire veilingen</h1>
        <div class="row">
            <?php
            queryHomepageVoorwerpen("SELECT * FROM ( SELECT *, ROW_NUMBER() OVER (ORDER BY voorwerpnummer ASC) AS rownumber
                          FROM voorwerp) AS TEST WHERE TEST.rownumber BETWEEN 2 AND 4");
            ?>
        </div>

        <h1>Nieuwe veilingen</h1>
        <div class="row">
        <?php
        queryHomepageVoorwerpen("SELECT TOP 3 * FROM voorwerp ORDER BY looptijdbeginveiling ASC");
        ?>
        </div>

        <h1>Laagste prijzen</h1>
        <div class="row">
            <?php
            queryHomepageVoorwerpen("SELECT TOP 3 * FROM voorwerp ORDER BY startprijs ASC");
            ?>
        </div>
<?php require('partial files\footer.php') ?>