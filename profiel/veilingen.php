<?php
include_once('..\partial files\header.php');
include_once('..\partial files\models\gebruiker.php');
include_once('..\partial files\models\voorwerp.php');
include_once('..\partial files\models\bestand.php');
include_once('..\partial files\models\bod.php');
include_once('..\partial files\models\miscellaneous.php');
include_once('..\partial files\sidebar.php');

$username = "";
if (isset($_SESSION["user"])) {
    $username = $_SESSION["user"];
}

if ($username == "") {
    header('Location: ../login.php');
}


?>
    <h1>Profiel</h1>
<?php loadProfileSidebar($username, 4) ?>
    <div class="col-sm-12">
        <h3>Veilingen</h3>
        <?php echoVeilingen($username) ?>
    </div>

<?php

include_once('..\partial files\footer.php');

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

    echo '  <div class="veilingitem">
                    <a href="/veiling.php?voorwerpnummer=' . $voorwerp->voorwerpnummer . '">
                        <img src="../pics/' . $image . '" alt="veilingsfoto" onError="this.onerror=null;this.src=\'../itemImages/' . $image . '\'">
                        <h4>' . $voorwerp->titel . '</h4>
                        <p>' . $beschrijving . '</p>
                        <p class="prijs">€' . $prijs . '</p>
                        <div class="veiling-info">
                            ' . date("d-m-Y H:m", strtotime($voorwerp->looptijdeindeveiling)) . ' 
                        </div>
                    </a>
                </div>';
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

    if ($nVeilingen < 1) {
        echo "U heeft momenteel geen veilingen";
    }

    echoPagination($nVeilingen, $itemsPerPage, $currentPage, $username);
}

function echoPagination($totalItems, $itemsPerPage, $currentPageNumber)
{
    global $username;
    $nPages = ceil($totalItems / $itemsPerPage);
    if ($nPages > 1) {
        if ($currentPageNumber > 1) {
            echo("<button onclick='location.href='./veilingen.php?page=" . ($currentPageNumber - 1) . "''>Previous</button>");
        }
        if ($nPages > 9) {
            if ($currentPageNumber < 6) {
                for ($i = 1; $i < 10; $i++) {
                    echoPageNumber($i, $currentPageNumber);
                }
                echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
                echoPageNumber($nPages, $currentPageNumber);
            } else if ($currentPageNumber > ($nPages - 5)) {
                echoPageNumber(1, $currentPageNumber);
                echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
                for ($i = ($nPages - 8); $i < $nPages + 1; $i++) {
                    echoPageNumber($i, $currentPageNumber);
                }
            } else {
                echoPageNumber(1, $currentPageNumber);
                echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
                for ($i = ($currentPageNumber - 4); $i < $currentPageNumber + 5; $i++) {
                    echoPageNumber($i, $currentPageNumber);
                }
                echo '&nbsp; &nbsp;...&nbsp; &nbsp;';
                echoPageNumber($nPages, $currentPageNumber);
            }

        } else {
            for ($i = 1; $i < $nPages + 1; $i++) {
                echoPageNumber($i, $currentPageNumber);
            }
        }
        if ($currentPageNumber < $nPages) {
            echo("<button onclick=\"location.href='./veilingen.php?page=" . ($currentPageNumber + 1) . "'\">Next</button>");
        }
    }
}

function echoPageNumber($pageNumber, $currentPageNumber)
{
    global $username;
    if (($pageNumber) == $currentPageNumber) {
        echo '<b style="margin: 5px">' . $pageNumber . '</b>';
    } else {
        echo '<a style="margin: 5px" href=./veilingen.php?page=' . $pageNumber . '>' . $pageNumber . '</a>';
    }
}

?>