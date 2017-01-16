<?php
/**
 * Created by PhpStorm.
 * User: jamiel
 * Date: 16-12-2016
 * Time: 10:16
 */
include_once('../partial files/models/voorwerp.php');
include_once('../partial files/models/gebruiker.php');

/**
 * Ends the auction and emails the highest bidder and verkoper.
 * @param $voorwerpId ID of the voorwerp.
 */
function endAuction($voorwerpId) {
    if (!veilingEnded($voorwerpId)) {
        $verkoper = getVerkoperByVerkoopnummer($voorwerpId);
        $highestBidder = getTopBidderByVoorwerpnummer($voorwerpId);

        $isSendToVerkoper = mailAuctionEndedToVerkoper($verkoper, $voorwerpId);
        $isSendToHighestBidder = mailAuctionEndedToKoper($highestBidder, $voorwerpId);

        if ($isSendToVerkoper && $isSendToHighestBidder) {
            endVeilingByVoorwerpnummer($voorwerpId, $highestBidder->gebruikersnaam);
        }
    }
}

/**
 * Mails a gebruiker to notify that the given auction has ended.
 * @param $gebruiker The gebruiker to send the mail to. Expects the email attribute.
 * @param $voorwerpnummer The ID of the ended auction.
 * @return bool True if the mail has been send successfully.
 */
function mailAuctionEndedToVerkoper($gebruiker, $voorwerpnummer) {
    $email = $gebruiker->email;
    $headers = 'From: webmaster@eenmaalandermaal.com' . "\r\n" .
        'Reply-To: webmaster@eenmaalandermaal.com' . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    return mail($email,
        'EenmaalAndermaal: Veilingnummer: ' . $voorwerpnummer . ' afgelopen!',
    "De veiling met het veilingnummer: " . $voorwerpnummer . ' is afgelopen!', $headers);
}

function mailAuctionEndedToKoper($gebruiker, $voorwerpnummer) {
    $email = $gebruiker->email;
    $headers = 'From: webmaster@eenmaalandermaal.com' . "\r\n" .
        'Reply-To: webmaster@eenmaalandermaal.com' . "\r\n" .
        'MIME-Version: 1.0'. "\r\n" .
        'Content-Type: text/html; charset=ISO-8859-1' . "\r\n";

    return mail($email,
        'EenmaalAndermaal: Veilingnummer: ' . $voorwerpnummer . ' afgelopen!',
        "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
            <html xmlns='http://www.w3.org/1999/xhtml'>
                <head>
                    <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
                    <title>U ben overboden!</title>
                </head>
                <body style='font-family: Calibri,Candara,Segoe,Segoe UI,Optima,Arial,sans-serif; font-size: 18px'>
                    De veiling met het veilingnummer: " . $voorwerpnummer . " is afgelopen<br>
                    <a href=\"http://iproject2.icasites.nl/feedback.php?voorwerpnummer=". $voorwerpnummer ."\">Klik hier om feedback te geven aan verkoper.</a>
                </body>
            </html>", $headers);
}

?>