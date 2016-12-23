<?php
session_start();
if(isset($_GET['search'])){
    $search = $_GET['search'];
    $search = trim($search);
}
if(isset($_POST['logout'])){
    session_destroy();
    header('Location: #');
}
    ?>

<html lang="en">
<head>
    <title>EenmaalAndermaal</title>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://iproject2.icasites.nl/css/bootstrap.css">
    <link rel="stylesheet" href="http://iproject2.icasites.nl/css/style.css">
</head>
<body>

<header>
    <div class="headerbar">
    <div class="container headerbar-content">
    <div class="row">
        <a href="http://iproject2.icasites.nl/index.php">
            <img class="col-xs-12 col-sm-12 col-md-6 col-lg-5" src="http://iproject2.icasites.nl/images/logo.png" alt="Logo EenmaalAndermaal">
        </a>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-7 ">
            <?php if(isset($_SESSION['user'])){

                echo '
                <form action="http://iproject2.icasites.nl/index.php" method="post">
                     <input type="image" class="user img-circle" src="http://iproject2.icasites.nl/images/IconLogOut.png" alt="Loguit">
                     <input name="logout" type="hidden">
                 </form>   
                <a href="http://iproject2.icasites.nl/profiel.php">
                    <img class="user img-circle" src="http://iproject2.icasites.nl/images/IconGebruiker.png" alt="Gebruiker">';

                echo "<h4 class='welkom-message'>";
                echo $_SESSION['user'];
                echo "</h4></a>";
           }
           else{
                echo 
                '<a href="http://iproject2.icasites.nl/registreer.php">
                    <img class="user img-circle"  src="http://iproject2.icasites.nl/images/IconRegistreren.png" alt="Registreren">
                </a>
                <a href="http://iproject2.icasites.nl/login.php">
                    <img class="user img-circle" src="http://iproject2.icasites.nl/images/IconGebruiker.png" alt="Gebruiker">
                </a>';
            }?>
        
        </div>
        <form action='http://iproject2.icasites.nl/zoeken.php' method='GET'>
            <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5 search">
                <input placeholder="Zoeken naar..." type="text" class="search-bar" name="search" value="<?php
                    if(isset($search)){
                        echo $search;
                    }
                ?>" maxlength="100" required>
                <button type="submit" class="btn-search">Zoeken</button>
            </div>
        </form>
    </div>
    </div>
    </div>
</header>
<div class="container">
