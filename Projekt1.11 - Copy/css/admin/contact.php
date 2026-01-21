<?php
require_once("../cfg.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php'; 

// Formularz kontaktowy
function PokazKontakt()
{
    return '
    <h2>Formularz kontaktowy</h2>

    <form method="post" action="'.$_SERVER['REQUEST_URI'].'">
        <label>Twój email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Temat:</label><br>
        <input type="text" name="temat" required><br><br>

        <label>Treść wiadomości:</label><br>
        <textarea name="tresc" rows="6" required></textarea><br><br>

        <input type="submit" name="wyslij_kontakt" value="Wyślij wiadomość">
    </form>
    ';
}

// Funkcja wysyłająca mail przez Gmail SMTP
function WyslijMailKontakt()
{
    global $kontakt_email; // odbiorca maila z cfg.php

    if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
        echo "<p>Nie wypełniłeś wszystkich pól!</p>";
        echo PokazKontakt();
        return;
    }

    $subject = $_POST['temat'];
    $body    = $_POST['tresc'];
    $sender  = $_POST['email'];

    $mail = new PHPMailer(true);

    try {
        // Konfiguracja SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'paulapaulina22711@gmail.com';       
        $mail->Password = 'uxth opus bbqu zffs';           
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom($sender, 'Formularz kontaktowy');
        $mail->addAddress($kontakt_email); 

        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        echo "<p>Wiadomość została wysłana!</p>";
    } catch (Exception $e) {
        echo "<p>Nie udało się wysłać wiadomości. Błąd: {$mail->ErrorInfo}</p>";
    }
}

// Funkcja przypomnienia hasła przez Gmail SMTP
function PrzypomnijHaslo() {
    global $login, $pass, $admin_email;

    $subject = "Przypomnienie danych logowania";
    $body    = "Twój login: $login\nTwoje hasło: $pass";

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'paulapaulina22711@gmail.com';  
        $mail->Password = 'uxth opus bbqu zffs';      
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('paulapaulina22711@gmail.com', 'System strony');
        $mail->addAddress($admin_email);

        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        echo "<p>Hasło zostało wysłane na $admin_email</p>";
    } catch (Exception $e) {
        echo "<p>Błąd wysyłki! {$mail->ErrorInfo}</p>";
    }
}
?>
