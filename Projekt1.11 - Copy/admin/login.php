<!-- Moduł implementujący logowanie admina. -->

<?php

    require_once('../cfg.php');
    require_once('contact.php');

    //Sprawdzenie czy admin jest już zalogowany, jeżeli tak przenosi do admin dashboard.
    if(isset($_SESSION['admin'])){
        header("Location: adminDashboard.php");
        exit;
    }

    $error = '';

    //Sprawdzenie czy dane logowania są poprawne.
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS) ?? "";
        $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS) ?? "";

        if($username === $login && $password === $pass){
            $_SESSION["admin"] = true;
            header('Location: adminDashboard.php');
            exit;
        }
        else {
            $error = "Nieprawidłowe dane logowania";
            }
    }

    //Obsługa opcji przypominienia hasła i kontaktu 
    $action = $_GET['action'] ?? 'login';

    if ($action === 'forgot') {
        PrzypomnijHaslo();
        echo "<p>Hasło zostało wysłane!</p>";
        exit;
    }

    if ($action === 'contact') {
        if (isset($_POST['wyslij_kontakt'])) {
            WyslijMailKontakt();
        } else {
            echo PokazKontakt();
        }
        exit;
    }


?>

<!-- Formularz logowania admina. -->

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styleadmin.css">
    <title>Logowanie, panel admina</title>
</head>
<body class="strona-logowania">
    <header>
        <h1>Logowanie do panelu admina</h1>
    </header>
    <div class="logowanie"> 
        <?php if($error): ?>
            <p><?= $error ?></p>
        <?php endif; ?>
        <form method="post" name="login">
            <label>Login:<br>
                <input type="text" name="username" required>
            </label><br>
            <label>Hasło:<br>
                <input type="password" name="password" required>
            </label><br>
            <input type="submit" name="submit" value="Zaloguj"><br>
        </form>
        <div class="login-links">
            <a href="login.php?action=forgot">Przypomnij hasło</a> |
            <a href="login.php?action=contact">Kontakt</a>
        </div>
    </div>
</body>
</html>

