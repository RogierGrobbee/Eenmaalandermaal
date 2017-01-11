<?php include_once('partial files\databaseconnection.php');
$errorMessage;

if (!empty($_POST['wachtwoord'])) {
    $password = $_POST['wachtwoord'];

    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number = preg_match('@[0-9]@', $password);
}
if (isset($_POST['Login'])) {
    if (
        empty($_POST['gebruikersnaam']) ||
        empty($_POST['wachtwoord'])
    ) {
        $errorMessage = "Niet alles is ingevuld.";
    } else if (!$uppercase || !$lowercase || !$number || strlen($password) < 8) {
        $errorMessage = "Wachtwoord moet minimaal 8 characters lang zijn en 1 kleine letter, 1 hoofdletter en een nummer bevatten.";
    } else
        if (getValidation($_POST['gebruikersnaam'])) {
            $password = $_POST['wachtwoord'];
            $username = $_POST['gebruikersnaam'];
            $hash = getPassword($username);

            if (password_verify($password, $hash)) {
                session_start();
                $_SESSION['user'] = $username;
                header('Location: index.php');
            } else {
                $errorMessage = 'Combinatie gebruikersnaam en wachtwoord zijn onjuist.';
            }
        } else {
            $errorMessage = 'Combinatie gebruikersnaam en wachtwoord zijn onjuist.';
        }
}

$rubriekArray = loadRubrieken();
include_once('partial files\header.php');
?>

    <h1>Log In</h1>

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