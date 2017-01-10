<?php
function loadJSScripts() {
    echo '<script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>';
    echo '<script type="text/javascript" src="js/countdown.js"></script>';
}

require('partial files\models\rubriek.php');
require('partial files\models\voorwerp.php');
require('partial files\models\bestand.php');
require('partial files\models\bod.php');
require('partial files\header.php');

if(isset($_SESSION['user'])){
    echo "<h1>Welkom ". $_SESSION['user'] ."!</h1>";
}
else {
    echo "<h1>Meer dan 2000 veilingen! Bied nu!</h1>";
}

function echoHomepageVoorwerp($voorwerp, $prijs, $image){
    if($prijs < 1){
        $prijs = "0".$prijs;
    }

    echo '<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 homepage-veiling">
            <a href="veiling.php?voorwerpnummer='.$voorwerp->voorwerpnummer.'">
            <img src="pics/'. $image .'"alt="veiling">
            <h4>'.$voorwerp->titel.'</h4>
            <div class="homepage-veiling-prijstijd">€'. $prijs .'<br>
            <span data-tijd="'. $voorwerp->looptijdeindeveiling .'" class="tijd"></span></div>
            <button class="veiling-detail btn-homepage">Bied</button></a></div>';
}

function queryHomepageVoorwerpen($queryString)
{
    $voorwerpen = getVoorwerpenByQuery($queryString);

    $count = 0;

    foreach($voorwerpen as $voorwerp){
        $image = loadBestandByVoorwerpnummer($voorwerp->voorwerpnummer);
        $biedingen = getBiedingenByVoorwerpnummer($voorwerp->voorwerpnummer);

        if($biedingen == null){
            $prijs = $voorwerp->startprijs;
        }
        else{
            $prijs = $biedingen[0]->bodbedrag;
        }

        $count++;
        echoHomepageVoorwerp($voorwerp, $prijs, $image);
    }

    if($count == 0){
        echo "<div class='error'>U heeft nog niet geboden op een veiling.</div>";
    }
}

//sidebar maken op basis van rubrieken
require('partial files\sidebar.php');
$rubriekArray = loadAllRubrieken();
loadRubriekenSidebar(null);
?>
    <?php
        $voorwerp = getFeaturedVoorwerp();
        $biedingen = getBiedingenByVoorwerpnummer($voorwerp->voorwerpnummer);

        if($biedingen == null){
            $prijs = $voorwerp->startprijs;
        }
        else{
            $prijs = $biedingen[0]->bodbedrag;
        }

        $image = loadBestandByVoorwerpnummer($voorwerp->voorwerpnummer);
        echo '<a href="veiling.php?voorwerpnummer='.$voorwerp->voorwerpnummer.'">
        <img src="pics/'.$image.'" alt="homepage featured" class="homepage-featured-img">';

        echo '<div class="col-lg-4 col-md-5 col-sm-7 homepage-featured-detail">
            <h2>'. $voorwerp->titel .'</h2>
            <div class="homepage-featured-prijs">€'. $prijs .'<br>
                <span data-tijd="'. $voorwerp->looptijdeindeveiling .'"
                 data-nummer= "' . $voorwerp->voorwerpnummer . '" class="tijd"></span>
            </div>
            <button class="veiling-detail btn-homepage">Bied</button></div></a>' ?>

        <?php
            if(isset($_SESSION['user'])){
                echo "<h1>Uw meest recente geboden veilingen</h1>
                    <div class='row'>";

                queryHomepageVoorwerpen("SELECT TOP 3 v.voorwerpnummer,v.titel,v.beschrijving,v.startprijs,
                v.looptijdeindeveiling, max(b.bodtijdstip) as bodtijdstip FROM voorwerp v INNER JOIN bod b ON b.voorwerpnummer=v.voorwerpnummer
                    WHERE b.gebruikersnaam = '". $_SESSION['user']. "' GROUP BY v.voorwerpnummer,v.titel,v.beschrijving,v.startprijs,
                v.looptijdeindeveiling ORDER BY max(b.bodtijdstip) DESC");

                echo "</div>";
            }
        ?>


        <h1>Meest populaire veilingen</h1>
        <div class="row">
            <?php
            queryHomepageVoorwerpen("SELECT * FROM ( SELECT v.voorwerpnummer,v.titel,v.beschrijving,v.startprijs,
                                    v.looptijdeindeveiling, count(v.voorwerpnummer) as 'aantal biedingen', 
                                    ROW_NUMBER() OVER (ORDER BY count(b.voorwerpnummer)  DESC) AS rownumber
                                    FROM voorwerp v INNER JOIN Bod b ON v.voorwerpnummer=b.voorwerpnummer
									WHERE looptijdeindeveiling > DATEADD(MINUTE, 1, GETDATE())
                                    GROUP BY v.voorwerpnummer,v.titel,v.beschrijving,v.startprijs,
                                    v.looptijdeindeveiling) AS rows WHERE rows.rownumber BETWEEN 2 AND 4");
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
            queryHomepageVoorwerpen("EXECUTE sp_SearchVoorwerpenByTitle @search=' ', @searchCount=0, @nSkippedRecords=0, @itemPerPage=3, @filter='laagstebod'");
            ?>
        </div>
<?php require('partial files\footer.php') ?>