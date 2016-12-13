<?php
    if(isset($_GET['search'])){
        $search = $_GET['search'];
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
