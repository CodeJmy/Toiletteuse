<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * Envoie un e-mail via PHPMailer
 *
 * @param string $destinataire Email du destinataire
 * @param string $nom Nom du destinataire (facultatif)
 * @param string $sujet Sujet de l'e-mail
 * @param string $message Corps HTML de l'e-mail
 * @return bool true si envoyé, false sinon
 */
function envoyerMail($destinataire, $nom, $sujet, $message)
{
    $mail = new PHPMailer(true);

    try {
        // Configuration SMTP
        $mail->isSMTP();
        $mail->Host = 'in-v3.mailjet.com';           // Remplace par ton serveur SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'exemple.com';  // Remplace par ton email
        $mail->Password = 'azerty12';           // Ton mot de passe SMTP
        $mail->SMTPSecure = 'ssl';                  // Ou 'ssl'
        $mail->Port = 465;                          // 465 pour 'ssl'

        // Informations de l'expéditeur
        $mail->setFrom('exemple@gmail.com', 'toiletteuse');

        // Destinataire
        $mail->addAddress($destinataire, $nom);

        // Contenu
        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erreur PHPMailer : " . $mail->ErrorInfo);
        return false;
    }
}
