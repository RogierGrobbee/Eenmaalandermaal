<?php
include_once('..\partial files\header.php');
include_once('..\partial files\models\gebruiker.php');
include_once('..\partial files\models\voorwerp.php');
include_once('..\partial files\models\gebruikerstelefoon.php');
include_once('..\partial files\sidebar.php');
include_once('..\partial files\models\bestand.php');
include_once('..\partial files\models\feedback.php');
include_once('..\partial files\models\bod.php');
include_once('..\partial files\models\miscellaneous.php');

$username = "";
if (isset($_GET["user"])) {
    $username = $_GET["user"];
} else {
    if (isset($_SESSION["user"])) {
        $username = $_SESSION["user"];
    }
}

$loggedIn = false;
if (isset($_SESSION["user"])) {
    $sessionUsername = $_SESSION["user"];
    if ($username == $sessionUsername) {
        $loggedIn = true;
    }
}

if ($username == "") {
    header('Location: ../login.php');
}

$user = getUserByUsername($username);
$ratingList = getRatingsByUser($username);
if ($user == null) {
    echo '<h1>Profiel van ' . $username . '</h1>
    <div class="col-sm-12">';
    if (isset($_SESSION['message'])) {
        echo "<div class='alert alert-success'>
                <strong>" . $_SESSION['message'] . "</strong>
              </div>";
        unset($_SESSION['message']);
    }

    echo 'Geen gebruiker gevonden
    </div>';
} else {
if ($loggedIn) {
    ?>
    <h1>Profiel</h1>
    <?php loadProfileSidebar($username, 1) ?>
    <div class="col-sm-12">
        <h3>Overzicht</h3>
        <div class="well">
            Username: <?php echo $user->gebruikersnaam; ?>
            <br>Naam: <?php echo $user->voornaam . " " . $user->achternaam ?>
            <br>Email: <?php echo $user->email ?>
            <br> <?php echoPhoneNumbers()
            ?>
        </div>
        <h3>Adres</h3>
        <div class="well">
            Adres: <?php echo $user->adresregel1; ?>
            <br>Postcode: <?php echo $user->postcode; ?>
            <br>Plaats: <?php echo $user->plaatsnaam; ?>
            <br>Land: <?php echo $user->land; ?>
        </div>
    </div>

    <?php
} else { ?>

<h1>Profiel van <?php echo $username ?></h1>

<div class="col-sm-12">
    <?php
    if (isset($_SESSION['message'])) {
        echo "<div class='alert alert-success'>
                <strong>" . $_SESSION['message'] . "</strong>
              </div>";
        unset($_SESSION['message']);
    }
    ?>

    <h3>Overzicht</h3>
    <div class="well">
        Username: <?php echo $user->gebruikersnaam; ?>
        <br>Naam: <?php echo $user->voornaam . " " . $user->achternaam ?>
        <br>Email: <?php echo $user->email ?>
        <br> <?php //echoPhoneNumbers($user)
        ?>
    </div>
    <h3>Beoordeling</h3>
    <div class="well">
        <div style="display: inline-block">
            <div style="float: left; margin-right: 10px;">
                <img width="50px" src="../images/positief.png">
                <div style="text-align: center">
                    <label><?php echo echoRating("positief") ?></label>
                </div>
            </div>
            <div style="float: left; margin-right: 10px;">
                <img width="50px" src="../images/neutraal.png">
                <div style="text-align: center">
                    <label><?php echo echoRating("neutraal") ?></label>
                </div>
            </div>
            <div style="float: left; margin-right: 10px;">
                <img width="50px" src="../images/negatief.png">
                <div style="text-align: center">
                    <label><?php echo echoRating("negatief") ?></label>
                </div>
            </div>
        </div>

        <h4>3 meest recente beoordelingen</h4>
        <br>
        <div style="display: inline-block">
            <?php echoTop3Ratings() ?>
        </div>
    </div>
    <h3>Veilingen</h3>
    <?php echoVeilingen($username) ?>


    <?php
    }
    }


    include_once('..\partial files\footer.php');

    function echoTop3Ratings()
    {
        global $username;

        $ratingList = getTop3Ratings($username);
        foreach ($ratingList as $rating) {
            echo '<blockquote style="background-color: white; float: left; margin-right: 10px;">';
            echo '<div>';
            if ($rating->feedbacksoort == 'positief') {
                echo '<img width="20px" src="../images/positief.png"> &nbsp;';
            } else if ($rating->feedbacksoort == 'neutraal') {
                echo '<img width="20px" src="../images/neutraal.png"> &nbsp;';
            } else if ($rating->feedbacksoort == 'negatief') {
                echo '<img width="20px" src="../images/negatief.png"> &nbsp;';
            }

            echo strip_tags($rating->commentaar);
            echo '</div>';
            echo '<p style="color: gray; font-size: medium; float: left">'.$rating->gebruikersnaam.'</p>';
            echo '<p style="color: gray; font-size: medium; float: right">'.date("d-m-Y H:m", strtotime($rating->dagtijdstip)).'</p>';
            echo '</blockquote > ';
        }

    }

    function echoRating($ratingSoort)
    {
        global $ratingList;

        foreach ($ratingList as $rating) {
            if ($rating->feedbacksoort == $ratingSoort) {
                return $rating->aantal;
            }
        }
        return 0;
    }

    function echoPhoneNumbers()
    {
        echo '<br > ';
        global $username;

        $phoneNumbersObjects = getPhoneNumbers($username);
        if (isset($_POST['remove'])) {
            foreach ($phoneNumbersObjects as $k => $number) {
                if ($number->telefoon == $_POST['remove']) {
                    unset($phoneNumbersObjects[$k]);
                    removePhoneNumber($username, $_POST['remove']);
                }
            }
        }
        $phoneNumbersObjects = getPhoneNumbers($username);
        $phoneNumbers = array();
        echo 'Telefoonnummers:';
        if (count($phoneNumbersObjects) < 2 && !isset($_POST['telefoon'])) {
            echo ' < div class="row" ><p style = "float: left; margin-left: 20px" > ' . $phoneNumbersObjects[0]->telefoon . '</p ></div > ';
        } else {
            foreach ($phoneNumbersObjects as $k => $number) {
                echo '<div class="row" ><p style = "float: left; margin-left: 20px" > ' . $number->telefoon . '</p > <form method = "post" style = "float:left" ><button style = "float:left" type = "submit" name = "remove" value = "' . $number->telefoon . '" > X</button ></form ></div > ';
                array_push($phoneNumbers, $number->telefoon);
            }
            if (isset($_POST['telefoon'])) {
                if (is_numeric($_POST['telefoon'])) {
                    if (!in_array($_POST['telefoon'], $phoneNumbers)) {
                        $temp = $phoneNumbersObjects[count($phoneNumbersObjects) - 1];
                        $hoogsteVolgnr = $temp->volgnr;
                        $hoogsteVolgnr++;

                        insertPhoneNumber($hoogsteVolgnr, $username, $_POST['telefoon']);
                        echo ' < div class="row" ><p style = "float: left; margin-left: 20px" > ' . $_POST['telefoon'] . ' </p ><form method = "post" style = "float: left" ><button style = "float: left" type = "submit" name = "remove" value = "' . $_POST['telefoon'] . '" > X</button > </form ></div > ';
                    }
                }
            }
        }


        ?>
        <form method="post">
            <br>
            <div class="row">
                <div class="col-lg-3 col-md-5 col-sm-7 col-xs-12 add-number">
                    <input class="input-add-number" maxlength="10" value="" type="text" name="telefoon">
                </div>
                <div class="col-lg-3 col-md-4 col-sm-5 col-xs-12 add-number">
                    <button class="btn-add-number" type="submit" name="Toevoegen" value="toevoegen">Nummer toevoegen
                    </button>
                </div>
            </div>
        </form>
        <?php
    }

    function echoVoorwerp($voorwerp, $prijs, $image)
    {
        $beschrijving = $voorwerp->beschrijving;

        $beschrijving = strip_html_tags($beschrijving);

        if (strlen($beschrijving) > 300) {
            $beschrijving = substr($beschrijving, 0, 280) . "... <span>lees verder</span>";
        }

        if ($prijs < 1) {
            $prijs = "0" . $prijs;
        }

        echo '  <div class="veilingitem" >
                    <a href = "/veiling.php?voorwerpnummer=' . $voorwerp->voorwerpnummer . '" >
                        <img src = "../pics/' . $image . '" alt = "veilingsfoto" >
                        <h4 > ' . $voorwerp->titel . '</h4 >
                        <p > ' . $beschrijving . '</p >
                        <p class="prijs" >€' . $prijs . ' </p >
                        <div class="veiling-info" >
' . date("d-m-Y H:m", strtotime($voorwerp->looptijdeindeveiling)) . ' 
                        </div >
                    </a >
                </div > ';
    }

    function echoVeilingen($username)
    {
        $itemsPerPage = 10;
        $currentPage = 1;
        if (isset($_GET['page'])) {
            if (is_numeric($_GET['page'])) {
                $currentPage = $_GET['page'];
            }
        }

        $nVeilingen = countVoorwerpenByVerkoper($username);
        $voorwerpList = getVoorwerpenByVerkoper($username, $currentPage, $itemsPerPage);

        foreach ($voorwerpList as $voorwerp) {

            $biedingen = getBiedingenByVoorwerpnummer($voorwerp->voorwerpnummer);
            if ($biedingen == null) {
                $prijs = $voorwerp->startprijs;
            } else {
                $prijs = $biedingen[0]->bodbedrag;
            }

            $foto = loadBestandByVoorwerpnummer($voorwerp->voorwerpnummer);
            echoVoorwerp($voorwerp, $prijs, $foto);
        }

        if (count($voorwerpList) < 1){

            echo '<div class="well">Deze gebruiker heeft geen veilingen</div>';
        }

        if ($nVeilingen < 2) {
            echoPagination($nVeilingen, $itemsPerPage, $currentPage, $username);
        }
    }

    function echoPagination($totalItems, $itemsPerPage, $currentPageNumber)
    {
        global $username;
        $nPages = ceil($totalItems / $itemsPerPage);
        if ($currentPageNumber > 1) {
            echo("<button onclick='location . href = './overzicht.php?user=' . $username . '&page=" . ($currentPageNumber - 1) . "''>Previous</button>");
        }
        if ($nPages > 9) {
            if ($currentPageNumber < 6) {
                for ($i = 1; $i < 10; $i++) {
                    echoPageNumber($i, $currentPageNumber);
                }
                echo ' & nbsp; &nbsp;...&nbsp; &nbsp;';
                echoPageNumber($nPages, $currentPageNumber);
            } else if ($currentPageNumber > ($nPages - 5)) {
                echoPageNumber(1, $currentPageNumber);
                echo ' & nbsp; &nbsp;...&nbsp; &nbsp;';
                for ($i = ($nPages - 8); $i < $nPages + 1; $i++) {
                    echoPageNumber($i, $currentPageNumber);
                }
            } else {
                echoPageNumber(1, $currentPageNumber);
                echo ' & nbsp; &nbsp;...&nbsp; &nbsp;';
                for ($i = ($currentPageNumber - 4); $i < $currentPageNumber + 5; $i++) {
                    echoPageNumber($i, $currentPageNumber);
                }
                echo ' & nbsp; &nbsp;...&nbsp; &nbsp;';
                echoPageNumber($nPages, $currentPageNumber);
            }

        } else {
            for ($i = 1; $i < $nPages + 1; $i++) {
                echoPageNumber($i, $currentPageNumber);
            }
        }
        if ($currentPageNumber < $nPages) {
            echo("<button onclick=\"location.href=' ./overzicht . php ? user = '.$username.' & page = " . ($currentPageNumber + 1) . "'\">Next</button>");
        }
    }

    function echoPageNumber($pageNumber, $currentPageNumber)
    {
        global $username;
        if (($pageNumber) == $currentPageNumber) {
            echo ' < b style = "margin: 5px" > ' . $pageNumber . '</b > ';
        } else {
            echo '<a style = "margin: 5px" href =./overzicht . php ? user = ' . $username . ' & page = ' . $pageNumber . ' > ' . $pageNumber . '</a > ';
        }
    }

    ?>
