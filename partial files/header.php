<?php
session_start();
    if(isset($_GET['search'])){
        $search = $_GET['search'];
    }
    if(isset($_POST['logout'])){
        session_destroy();
        echo "test";
        header('Location: #');
    }
?>

<html lang="en">
<head>
    <title>EenmaalAndermaal</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <div class="headerbar">
    <div class="container headerbarContent">
    <div class="row">
        <a href="index.php">
            <img class="col-xs-12 col-sm-8 col-md-6 col-lg-5" src="images/logo.png" alt="Logo EenmaalAndermaal">
        </a>
        <div class="col-xs-12 col-sm-4 col-md-6 col-lg-7 ">
            <?php if(isset($_SESSION['user'])){
                echo '
                <form action="index.php" method="post">
                     <input type="image" class="user img-circle" src="images/IconLogOut.png" alt="Loguit">
                     <input name="logout" type="hidden">
                 </form>   
                <a href="profiel.php">
                    <img class="user img-circle" src="images/IconGebruiker.png" alt="Gebruiker">
                </a>';
           }
           else{
                echo 
                '<a href="registreer.php">
                    <img class="user img-circle"  src="images/IconRegistreren.png" alt="Registreren">
                </a>
                <a href="login.php">
                    <img class="user img-circle" src="images/IconGebruiker.png" alt="Gebruiker">
                </a>';
            }?>
        
        </div>
        <form action='zoeken.php' method='GET'>
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 search">
                <input type="text" class="search-bar" name="search" value="<?php
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
