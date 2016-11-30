<?php




function loadVeilingItems($rubriekId)
{
    include ('loadBestanden.php');
    if (is_numeric($rubriekId)) {
        $db = new PDO ("sqlsrv:Server=LAPTOP-AOSH53E4\SQLEXPRESS;Database=eenmaalandermaal;ConnectionPooling=0", "sa", "Kanarie//////////");
        $query = $db->query("select * from voorwerp where voorwerpnummer in(
	                          select voorwerpnummer from voorwerpinrubriek where rubriekoplaagsteniveau in
	                          (
		                        select rubrieknummer from rubriek where superrubriek='".$rubriekId."' or rubrieknummer = '".$rubriekId."'
	                          )
                            )");
        while ($voorwerp = $query->fetch(PDO::FETCH_OBJ)) {

            $list = loadbestanden($voorwerp->voorwerpnummer);
            $image = $list != null ? $list[0] : "NoImageAvalible.jpg";
          echo '  <div class="veilingitem">
                    <a href="./veiling.php?voorwerpnummer='.$voorwerp->voorwerpnummer.'">
                        <img src="./bestanden/'.$image.'" alt="veilingsfoto">
                        <h4>'. $voorwerp->titel .'</h4>
                        <p>' . $voorwerp->beschrijving . '</p>
                        <p class="prijs">€' . $voorwerp->startprijs . '</p> <div class="veilingInfo"><span class="tijd">'.$voorwerp->looptijdeindeveiling.'</span> <button class="veilingDetail">Meer informatie</button> </div>
                    </a>
                </div>';
        }
    }
    else {
        echo 'Rubriek niet gevonden';
    }
}

?>
