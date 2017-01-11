<?php include_once('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();
include_once('partial files\header.php');
?>

    <h1>Test</h1>

<?php include_once('partial files\sidebar.php');
loadRubriekenSidebar(null);
?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<?php
$query = $db->query("SELECT * FROM rubriek WHERE superrubriek is null ORDER BY rubrieknaam");


foreach ($query as $row) {
    $id = $row['rubrieknummer'];
    $idString = "#" . $row['rubrieknummer'];

    echo "<a href='" . $idString . "' data-toggle='collapse'>" . $row['rubrieknaam'] . "</a>";
    echo "<br>";
    echo "<div id='" . $id . "' class='collapse margin-left'>";

    $query2 = $db->query("SELECT * FROM rubriek WHERE superrubriek = $id ORDER BY rubrieknaam");
    foreach ($query2 as $row2) {
        $id2 = $row2['rubrieknummer'];
        $idString2 = "#" . $row2['rubrieknummer'];
        echo "<a href='" . $idString2 . "' data-toggle='collapse'>" . $row2['rubrieknaam'] . "</a>";
        echo "<br>";
        echo "<div id='" . $id2 . "' class='collapse margin-left'>";

        $query3 = $db->query("SELECT * FROM rubriek WHERE superrubriek = $id2 ORDER BY rubrieknaam");
        foreach ($query3 as $row3) {
            $id3 = $row3['rubrieknummer'];
            $idString3 = "#" . $row3['rubrieknummer'];
            echo "<a href='" . $idString3 . "' data-toggle='collapse'>" . $row3['rubrieknaam'] . "</a>";
            echo "<br>";
            echo "<div id='" . $id3 . "' class='collapse margin-left'>";

            $query4 = $db->query("SELECT * FROM rubriek WHERE superrubriek = $id3 ORDER BY rubrieknaam");
            foreach ($query4 as $row4) {
                $id4 = $row4['rubrieknummer'];
                $idString4 = "#" . $row4['rubrieknummer'];
                echo "<a href='" . $idString4 . "' data-toggle='collapse'>" . $row4['rubrieknaam'] . "</a>";
                echo "<br>";
            }
            echo "</div>";
        }
        echo "</div>";
    }
    echo "</div>";
}

?>
<?php include_once('partial files\footer.php') ?>