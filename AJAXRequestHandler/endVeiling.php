<?php
/**
 * Created by PhpStorm.
 * User: jamiel
 * Date: 16-12-2016
 * Time: 10:16
 */

include_once('../partial files/databaseconnection.php');

/**
 * Ends the auction and emails the highest bidder and verkoper.
 * @param $voorwerpId ID of the voorwerp.
 */
function endAuction($voorwerpId) {
    if (!veilingEnded($voorwerpId)) {
        $verkoper = getVerkoperByVoorwerpnummer($voorwerpId);
        $highestBidder = getHighestBidderByVoorwerpnummer($voorwerpId);

        if (mailAuctionEndedToGebruiker($verkoper, $voorwerpId)
            && mailAuctionEndedToGebruiker($highestBidder, $voorwerpId)) {
            endVeilingByVoorwerpnummer($voorwerpId);
        }
    }
}

/**
 * Mails a gebruiker to notify that the given auction has ended.
 * @param $gebruiker The gebruiker to send the mail to. Expects the email attribute.
 * @param $voorwerpnummer The ID of the ended auction.
 * @return bool True if the mail has been send successfully.
 */
function mailAuctionEndedToGebruiker($gebruiker, $voorwerpnummer) {
    $email = $gebruiker->email;
    $headers = 'From: webmaster@eenmaalandermaal.com' . "\r\n" .
        'Reply-To: webmaster@eenmaalandermaal.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    return mail($email,
        'EenmaalAndermaal: Veilingnummer: ' . $voorwerpnummer . ' afgelopen!',
    "De veiling met het veilingnummer: " . $voorwerpnummer . ' is afgelopen!', $headers);
}

?>