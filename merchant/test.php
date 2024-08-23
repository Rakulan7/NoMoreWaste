<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require '../vendor/autoload.php';

$env = parse_ini_file('../.env');
$merchant_email = "s.rakulan04@gmail.com";

//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {                   

    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Host       = $env["HOST"];
    $mail->SMTPAuth   = true;
    $mail->Username   = $env["MAIL"];
    $mail->Password   = $env["PASSWORD"];
    $mail->Port       = 587;

    $mail->setFrom('no-reply@nomorewaste.fr', 'no-reply@nomorewaste.fr');
    $mail->addAddress($merchant_email);

    $mail->CharSet = 'UTF-8';  // Set the character set to UTF-8
    $mail->Encoding = 'base64'; // Use base64 encoding for the content

    $mail->isHTML(true);
    $mail->Subject = "Confirmation de création de collecte";
    $mail->Body    = "
        <p>Bonjour,</p>
        <p>Votre collecte a été créée avec succès.</p>
        <p><strong>Détails de la collecte:</strong><br>
        Date: 10/08/2023<br>
        Heure: 10h20<br>
        Lieu: 45 rue raymond counil 77500 CHELLES</p>
        <p>Merci de votre contribution!</p>";

    $mail->AltBody = "Bonjour,\n\nVotre collecte a été créée avec succès.\n\nDétails de la collecte:\nDate: 10/08/2023\nHeure: 10h20\nLieu: 45 rue raymond counil 77500 CHELLES\n\nMerci de votre contribution!";

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}