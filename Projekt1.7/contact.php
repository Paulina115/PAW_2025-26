<?php
require_once("cfg.php");   
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


function WyslijMailKontakt($odbiorca)
{
    if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
        echo "<p>Nie wypełniłeś wszystkich pól!</p>";
        echo PokazKontakt();
        return;
    }

    $subject = $_POST['temat'];
    $body    = $_POST['tresc'];
    $sender  = $_POST['email'];

    $headers  = "From: Formularz kontaktowy <".$sender.">\r\n";
    $headers .= "Reply-To: ".$sender."\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=utf-8\r\n";

    if (mail($odbiorca, $subject, $body, $headers)) {
        echo "<p>Wiadomość została wysłana!</p>";
    } else {
        echo "<p>Nie udało się wysłać wiadomości.</p>";
    }
}


function PrzypomnijHaslo($email_docelowy)
{
    global $login, $pass;

    $subject = "Przypomnienie hasła do panelu admina";
    $body    = "Twój login: {$login}\nTwoje hasło: {$pass}";
    
    $headers  = "From: System strony <no-reply@twojastrona.pl>\r\n";
    $headers .= "Content-Type: text/plain; charset=utf-8\r\n";

    if (mail($email_docelowy, $subject, $body, $headers)) {
        echo "<p>Hasło zostało wysłane na Twój e-mail!</p>";
    } else {
        echo "<p>Błąd podczas wysyłania maila.</p>";
    }
}
?>
