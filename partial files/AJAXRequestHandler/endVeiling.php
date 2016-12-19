<?php
/**
 * Created by PhpStorm.
 * User: jamiel
 * Date: 16-12-2016
 * Time: 10:16
 */

include_once('../databaseconnection.php');

function endVeiling($voorwerpId) {
    if (!veilingEnded($voorwerpId)) {
        $verkoper = getVerkoperByVoorwerpnummer($voorwerpId);
        $highestBidder = getHighestBidderByVoorwerpnummer($voorwerpId);

        mailVeilingEndedToGebruiker($verkoper, $voorwerpId);
        mailVeilingEndedToGebruiker($highestBidder, $voorwerpId);
    }
}

function mailVeilingEndedToGebruiker($gebruiker, $voorwerpnummer) {
    $email = $gebruiker->email;
    $headers = 'From: webmaster@eenmaalandermaal.com' . "\r\n" .
        'Reply-To: webmaster@eenmaalandermaal.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    mail($email,
        'EenmaalAndermaal: Veilingnummer: ' . $voorwerpnummer . ' Beëindigd!',
    "De veiling met het veilingnummer: " . $voorwerpnummer . ' is beëindigd!', $headers);
}

?>