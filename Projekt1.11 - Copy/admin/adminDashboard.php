<!-- Moduł implementujący strone główną admina. -->

<?php

    require_once('../cfg.php');

    if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../css/styleadmin.css">
    <title>Dashboard admina</title>
</head>
<body>
<header>
    <h1>Witaj w panelu admina</h1>
    <nav class="navbar">
        <ul>
            <li><a href="adminPagesManagment.php">Zarządzaj podstronami</a></li>
            <li><a href="adminProductsManagment.php">Zarządzaj produktami w sklepie</a></li>
            <li><a href="logout.php">Wyloguj</a></li>
        </ul>
    </nav>
</header>
</body>
</html>