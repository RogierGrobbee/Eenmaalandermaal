<?php include_once('partial files\databaseconnection.php');
$rubriekArray = loadRubrieken();
include_once('partial files\header.php'); ?>

    <h1>Registreer</h1>

<?php include_once('partial files\sidebar.php');
loadSidebar($rubriekArray, null);
?>
<row>
    <div class="col-sm-12">
        <h3>Vul hier uw validatiecode in:</h3>
        Validatiecode: <input type="text" name="validatiecode" >
        <br>
        <br>
    </div>
</row>
<?php echo $_COOKIE["validatieCookie"]; ?>
<?php include_once('partial files\footer.php') ?>