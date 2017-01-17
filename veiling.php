<?php
date_default_timezone_set("Europe/Amsterdam");

function loadJSScripts() {
    echo '<script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>';
    echo '<script type="text/javascript" src="js/countdown.js"></script>';
}
require('partial files\header.php');

if (isset($_GET['voorwerpnummer'])) {
    if (is_numeric($_GET['voorwerpnummer'])) {
        $voorwerpnummer = $_GET['voorwerpnummer'];
    } else {
        $voorwerpnummer = 0;
    }
} else {
    exit;
}


require('partial files\models\bod.php');
require('partial files\models\voorwerp.php');
require('partial files\models\rubriek.php');
require('partial files\models\voorwerpinrubriek.php');
require('partial files\models\bestand.php');
require('partial files\models\gebruiker.php');
require('partial files\models\miscellaneous.php');

$voorwerp = getVoorwerpById($voorwerpnummer);
$biedingen = getBiedingenByVoorwerpnummer($voorwerpnummer);
$error = "";

if(isset($_POST['bod'])){
    if(is_numeric($_POST['bod'])){
        if(insertNewBod()){
            if(!empty($biedingen)) {
                $to = getEmail($biedingen[0]->gebruikersnaam);
                $subject = 'U bent overboden';
                $message ="
                <!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
                <html xmlns='http://www.w3.org/1999/xhtml'>
                    <head>
                        <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
                        <title>U ben overboden!</title>
                    </head>
                    <body style='font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif; font-size: 18px'>
                        U ben overboden op artikel: " . $voorwerp->titel . "<br>
                        <a href=\"http://iproject2.icasites.nl/veiling.php?voorwerpnummer=". $voorwerp->voorwerpnummer ."\">Klik hier om de veiling te bekijken.</a>
                    </body>
                </html>";
                $headers = 'From: webmaster@eenmaalandermaal.com' . "\r\n" .
                    'Reply-To: webmaster@eenmaalandermaal.com' . "\r\n" .
                    'MIME-Version: 1.0'. "\r\n" .
                    'Content-Type: text/html; charset=ISO-8859-1' . "\r\n";

                mail($to, $subject, $message, $headers);
            }
        }
    }
}

$biedingen = getBiedingenByVoorwerpnummer($voorwerpnummer);
$inputRubriekId = getVoorwerpRubriekByVoorwerpnummer($voorwerpnummer);

$rubriekArray = loadAllRubrieken();
$huidigeRubriek = null;

foreach ($rubriekArray as $k => $rubriek) {
    if ($rubriek->rubrieknummer == $inputRubriekId) {
        $huidigeRubriek = $rubriek;
    }
}

require('partial files\navigatie.php');
echo "<h1>$voorwerp->titel</h1>";

require('partial files\sidebar.php');
loadRubriekenSidebar($navigatieArray[count($navigatieArray) - 1]);

$list = loadBestandenByVoorwerpnummer($voorwerp->voorwerpnummer);
$image = $list[0]->filenaam;
if(empty($list)){
    $image = "NoImageAvailable.png";
}

if($biedingen == null){
    $minimalePrijs = $voorwerp->startprijs + calculateIncrease($voorwerp->startprijs);
    //$minimalePrijs = number_format((float)$minimalePrijs, 2, '.', ',');
}
else {
    $minimalePrijs = $biedingen[0]->bodbedrag + calculateIncrease($biedingen[0]->bodbedrag);
    //$minimalePrijs = number_format((float)$minimalePrijs, 2, '.', '');
}

function insertNewBod(){
    global $voorwerp;
    global $error;

    $biedingen = getBiedingenByVoorwerpnummer($voorwerp->voorwerpnummer);

    if(date("d/m/y H:i:s", strtotime($voorwerp->looptijdeindeveiling)) < date('d/m/y H:i:s')){
        $error = "<div class='alert alert-danger error'>
                        <strong>De veiling is al afgelopen.</strong>
                      </div>";
        return false;
    }
    else if($biedingen == null){
        if($biedingen < $voorwerp->startprijs + calculateIncrease($voorwerp->startprijs)) {
            $error = "<div class='alert alert-danger error'>
                        <strong>U moet minimaal €".calculateIncrease($voorwerp->startprijs)." hoger bieden!</strong>
                      </div>";
            return false;
        }
    }
    else if($_SESSION['user'] == $biedingen[0]->gebruikersnaam){
        $error = "<div class='alert alert-danger error'>
                    <strong>U heeft al het hoogste bod!</strong>
                  </div>";
        return false;
    }
    else if($_POST['bod'] < $biedingen[0]->bodbedrag + calculateIncrease($biedingen[0]->bodbedrag)){
        $error = "<div class='alert alert-danger error'>
                        <strong>U moet minimaal €".calculateIncrease($biedingen[0]->bodbedrag)." hoger bieden!</strong>
                      </div>";
        return false;
    }

    if(insertBod($voorwerp, $_POST['bod'], $_SESSION['user'])){
        $error = "<div class='alert alert-success error'>
                        <strong>Er is succesvol een bod geplaatst!</strong>
                      </div>";
        return true;
    }
    else{
        $error = "<div class='alert alert-danger error'>
                        <strong>Er kan niet hoger geboden worden dan 100.000!</strong>
                      </div>";
        return false;
    }
}

function calculateIncrease($prijs){
    switch(true){
        case $prijs >= 5000:
            return 50;
            break;
        case $prijs >= 1000:
            return 10;
            break;
        case $prijs >= 500:
            return 5;
            break;
        case $prijs >= 50:
            return 1;
            break;
        default:
            return 0.50;
            break;
    }
}

function showBieden(){
    global $minimalePrijs;
    global $biedingen;
    global $voorwerp;

    if($_SESSION['user'] == $voorwerp->verkoper){
        echo "<div class='highest-bod'>Biedingen</div>";
    }
    else if(isset($_SESSION['user']) && date("Y-m-d H:i:s", strtotime($voorwerp->looptijdeindeveiling)) > date('Y-m-d H:i:s')){
        if($biedingen[0]->gebruikersnaam != $_SESSION['user']) {
            echo '<div class="bieden">
                    <form action="veiling.php?voorwerpnummer=' . $voorwerp->voorwerpnummer . '" method="post">
                        <label for="bied-bar">€</label>
                        <input type="number" class="bied-bar" name="bod" id="bied-bar" min=
                         "' . $minimalePrijs . '" max="100000" value="' . $minimalePrijs . '" step="0.01" required>
                        <input type="hidden" name="voorwerpnummer" value="' . $voorwerp->voorwerpnummer . '">
                        <button type="submit" class="btn-bied">Bied</button>
                    </form>
                </div>';
        }
        else{
            echo "<div class='highest-bod'>U heeft het hoogste bod</div>";
        }
    }
    else if(date("Y-m-d H:i:s", strtotime($voorwerp->looptijdeindeveiling)) < date('Y-m-d H:i:s')){

    }
    else{
        echo '<div class="bieden">
            <form action="login.php">
                <button type="submit">Bied nu mee!</button>
            </form>
        </div>';
    }
}

function echoSuggestedVoorwerp($voorwerp, $prijs, $image){
    if($prijs < 1){
        $prijs = "0".$prijs;
    }

    echo '<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 homepage-veiling">
            <a href="veiling.php?voorwerpnummer='.$voorwerp->voorwerpnummer.'">
            <img src="pics/'. $image .'" alt="veiling" onError="this.onerror=null;this.src=\'itemImages/'. $image . '\'">
            <h4>'.$voorwerp->titel.'</h4>
            <div class="homepage-veiling-prijstijd">€'. $prijs .'<br>
            <span data-tijd="'. $voorwerp->looptijdeindeveiling .'" class="tijd"></span></div>
            <button class="veiling-detail btn-homepage">Bied</button></a></div>';
}

function suggestedVoorwerpen($rubrieknummer)
{
    global $voorwerpnummer;
    $count = 0;
    $voorwerpen = getSuggestedVoorwerpen($rubrieknummer);

    foreach($voorwerpen as $voorwerp){
        if($voorwerp->voorwerpnummer != $voorwerpnummer && $count < 3){
            $image = loadBestandByVoorwerpnummer($voorwerp->voorwerpnummer);
            $biedingen = getBiedingenByVoorwerpnummer($voorwerp->voorwerpnummer);

            if ($biedingen == null) {
                $prijs = $voorwerp->startprijs;
            } else {
                $prijs = $biedingen[0]->bodbedrag;
            }

            echoSuggestedVoorwerp($voorwerp, $prijs, $image);
            $count++;
        }
    }
}


?>
    <div class="row">
        <?php
            if(isset($error)){
                echo $error;
            }
        ?>
        <?php echo '<img class="veiling-picture" src="pics/'.$image.'" alt="geveilde voorwerp" onError="this.onerror=null;this.src=\'itemImages/'. $image . '\'">' ?>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-7">
            <div class="boddetail">
                <div class="veilingtijd">
                    <span data-tijd="<?php echo $voorwerp->looptijdeindeveiling ?>"
                          data-nummer="<?= $voorwerp->voorwerpnummer ?>" class="tijd"></span>
                </div>

                <?php
                showBieden();

                if($_SESSION['user'] == $voorwerp->verkoper) {
                    echo "<div class='highest-bod'>Er zijn nog geen biedingen!</div>";
                }
                else if($biedingen == null){
                    echo "<div class='highest-bod'>Er zijn nog geen biedingen! Bied snel!</div>";
                }
                else {
                    for($i=0; $i<count($biedingen); $i++){
                        if($i == 0){
                            echo "<div class='highest-bod'>";
                        }
                        else {
                            echo "<div class='bod'>";
                        }

                        if($biedingen[$i]->bodbedrag < 1){
                            $bod = "0" . $biedingen[$i]->bodbedrag;
                        }
                        else{
                            $bod = $biedingen[$i]->bodbedrag;
                        }

                        echo "<div class='gebruikersnaam'>".$biedingen[$i]->gebruikersnaam."</div>
                        <div class='bodprijs'>€".$bod." </div>
                        <div class='bod-date'><span>". str_replace(" ", "<br>",
                                date("d/m/y H:i:s", strtotime($biedingen[$i]->bodtijdstip))) ."</span></div>
                        <br></div>";
                    }
                }

                if($voorwerp->startprijs < 1){
                    $prijs = "0" . $voorwerp->startprijs;
                }
                else{
                    $prijs = $voorwerp->startprijs;
                }

                echo "<div class='bod'><div class='gebruikersnaam'>Startprijs</div>
                        <div class='bod-date'><span>". str_replace(" ", "<br>",
                        date("d/m/y H:i:s", strtotime($voorwerp->looptijdbeginveiling))) ."</span></div>
                        <div class='bodprijs'>€".$prijs."</div><br></div>";?>
            </div>

            <p><?php echo "Dit voorwerp is aangeboden door <a style='color: #4d79ff' href='profiel/overzicht.php?user=$voorwerp->verkoper'>$voorwerp->verkoper</a> ($voorwerp->plaatsnaam, $voorwerp->land)"?></p>

            <h4>Beschrijving</h4>
            <p><?php echo strip_html_tags($voorwerp->beschrijving) ?></p>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-5 extrainfo">
            <h4>Betalingswijze en -instructie</h4>
            <p>
                <?php echo "$voorwerp->betalingswijze, $voorwerp->betalingsinstructie" ?>
            </p>

            <h4>Verzendkosten en -instructie</h4>
            <p>
                <?php
                    if($voorwerp->verzendkosten == 0) {
                        echo "Geen verzendkosten, $voorwerp->verzendinstructies";
                    }
                    else{
                        echo "€$voorwerp->verzendkosten, $voorwerp->verzendinstructies";
                    }
                ?>
        </div>
    </div>
<div class="row">
<?php
for($i = 1; $i < 4; $i++) {
    if(!empty($list[$i])){
        echo '<div class="xs-12 sm-12 md-12 col-lg-6">
            <img class="other-veiling-picture" src="./pics/' . $list[$i]->filenaam . '" alt="plaatje voorwerp" onError="this.onerror=null;this.src=\'itemImages/'. $list[$i]->filenaam . '\'">
        </div>';
    }
    else{
        echo '<div class="sm-3"></div>';
    }
}
?>
</div>
    <h2>Aanbevolen veilingen</h2>
<div class="row">
<?php
    suggestedVoorwerpen($inputRubriekId);
?>
</div>
<?php require('partial files\footer.php')?>