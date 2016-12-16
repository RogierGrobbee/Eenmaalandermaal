<?php
function loadJSScripts() {
    echo '<script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>';
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
        $biedingen = getVoorwerpBiedingen($voorwerp->voorwerpnummer);

        if($biedingen == null){
            $prijs = $voorwerp->startprijs;
        }
        else{
            $prijs = $biedingen[0]->bodbedrag;
        }

        $list = loadbestanden($voorwerp->voorwerpnummer);
        $image = $list != null ? $list[0] : "NoImageAvailable.jpg";
        echo '<a href="veiling.php?voorwerpnummer='.$voorwerp->voorwerpnummer.'">
        <img src="pics/'.$image.'" alt="homepage featured" class="homepage-featured-img">';

        echo '<div class="col-lg-4 col-md-5 col-sm-7 col-xs-11 homepage-featured-detail">
            <h2>'. $voorwerp->titel .'</h2>
            <div class="homepage-featured-prijs">€'. $prijs .'<br>
                <span data-tijd="'. $voorwerp->looptijdeindeveiling .'" class="tijd"></span>
            </div>
            <button class="veiling-detail btn-homepage">Bied</button></div></a>' ?>


        <h1>Meest populaire veilingen</h1>
        <div class="row">
            <?php
            queryHomepageVoorwerpen("SELECT * FROM ( SELECT v.voorwerpnummer,v.titel,v.beschrijving,v.startprijs,
                                    v.looptijdeindeveiling, count(v.voorwerpnummer) as 'aantal biedingen', 
                                    ROW_NUMBER() OVER (ORDER BY count(b.voorwerpnummer)  DESC) AS rownumber
                                    FROM voorwerp v RIGHT JOIN Bod b ON v.voorwerpnummer=b.voorwerpnummer
                                    GROUP BY v.voorwerpnummer,v.titel,v.beschrijving,v.startprijs,
                                    v.looptijdeindeveiling) AS rows WHERE rows.rownumber BETWEEN 2 AND 4 
                                    AND looptijdeindeveiling > DATEADD(MINUTE, 1, GETDATE())");
            ?>
        </div>

        <h1>Nieuwe veilingen</h1>
        <div class="row">
        <?php
        queryHomepageVoorwerpen("SELECT TOP 3 * FROM voorwerp WHERE looptijdeindeveiling > DATEADD(MINUTE, 1, GETDATE()) ORDER BY looptijdbeginveiling ASC");
        ?>
        </div>

        <h1>Laagste prijzen</h1>
        <div class="row">
            <?php
            queryHomepageVoorwerpen("EXECUTE sp_SearchVoorwerpenByTitle @search='%', @nSkippedRecords=0, @itemPerPage=3, @filter='laagstebod'");
            ?>
        </div>
<?php require('partial files\footer.php') ?>