<!-- Moduł implementujący funkcje zarządzania stronami. -->

<?php
    include('../cfg.php');
    if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;}

    //Funkcja wyświetlająca wszystkie podstrony
    function ListaPodstron(mysqli $mysqli){
        $query = "SELECT * FROM page_list ORDER BY id ASC";
        $result = $mysqli->query($query);

        $html = "<div class='podstrony'>
                    <h2>Lista podstron</h2>
                    <table class='admin-table'>
                        <tr>
                            <th>ID</th>
                            <th>Tytuł</th>
                            <th>Status</th>
                            <th>Opcje</th>
                        </tr>";

        while($row = $result->fetch_assoc()){
            $status = $row['status'] == 1 ? "Aktywna" : "Nieaktywna";

            $html .= "
                <tr>
                    <td>{$row['id']}</td>
                    <td>{$row['page_title']}</td>
                    <td>$status</td>
                    <td>
                        <a href='?action=edit&id={$row['id']}'>Edytuj</a> |
                        <a href='?action=delete&id={$row['id']}'
                            onclick='return confirm(\"Usunąć podstronę?\")'>Usuń</a>

                </tr>";
        }

        $html .= "</table></div>";
        return $html;
}


    //Funkcja do edytowania podstron
    function EdytujPodstrone(mysqli $mysqli, int $id){
        $clear_id = (int)$id;
        $query = "SELECT * FROM page_list WHERE id = $clear_id LIMIT 1";
        $result = $mysqli->query($query);

        if (!$result || $result->num_rows == 0) {
            return "<p>Nie znaleziono podstrony.</p>";
        }

        $row = $result->fetch_assoc();

        if (isset($_POST['save_page'])) {
            $title = $mysqli->real_escape_string($_POST['title']);
            $content = $mysqli->real_escape_string($_POST['content']);
            $status = isset($_POST['status']) ? 1 : 0;

            $update = "UPDATE page_list
                    SET page_title='$title', page_content='$content', status='$status'
                    WHERE id=$clear_id";

            if ($mysqli->query($update)) {
                echo "<p style='color:green;'>Zapisano zmiany!</p>";
            } else {
                echo "<p style='color:red;'>Błąd podczas zapisu!</p>";
            }
        }

        return "
            <h2>Edytuj podstronę</h2>
            <form method='post'>
                <p>
                    <label>Tytuł:</label><br>
                    <input type='text' name='title' value='{$row['page_title']}' style='width:400px;'>
                </p>
                <p>
                    <label>Treść:</label><br>
                    <textarea name='content' rows='10' cols='80'>{$row['page_content']}</textarea>
                </p>
                <p>
                    <label>
                        <input type='checkbox' name='status' ".($row['status']==1?'checked':'').">
                        Strona aktywna
                    </label>
                </p>
                <p>
                    <input type='submit' name='save_page' value='Zapisz zmiany'>
                </p>
            </form>
        ";
    }

    //Funkcja do dodawania podstrony
    function DodajPodstrone(mysqli $mysqli){
        if (isset($_POST['add_page'])) {
        $title = $mysqli->real_escape_string($_POST['title']);
        $content = $mysqli->real_escape_string($_POST['content']);
        $status = isset($_POST['status']) ? 1 : 0;

        $query = "INSERT INTO page_list (page_title, page_content, status)
                  VALUES ('$title', '$content', '$status')";

        if ($mysqli->query($query)) {
            echo "<p style='color:green;'>Dodano nową podstronę!</p>";
        } else {
            echo "<p style='color:red;'>Błąd podczas dodawania!</p>";
        }
    }

    return "
        <h2>Dodaj nową podstronę</h2>
        <form method='post'>
            <p>
                <label>Tytuł:</label><br>
                <input type='text' name='title' style='width:400px;'>
            </p>
            <p>
                <label>Treść:</label><br>
                <textarea name='content' rows='10' cols='80'></textarea>
            </p>
            <p>
                <label>
                    <input type='checkbox' name='status'>
                    Strona aktywna
                </label>
            </p>
            <p>
                <input type='submit' name='add_page' value='Dodaj podstronę'>
            </p>
        </form>
    ";
    }

    //Funkcja do usuwania podstrony
    function UsunPodstrone(mysqli $mysqli, int $id){
        $clear_id = (int)$id;
        $query = "DELETE FROM page_list WHERE id = $clear_id LIMIT 1";

        if ($mysqli->query($query)) {
            echo "<p style='color:green;'>Usunięto podstrone!</p>";
        } else {
            echo "<p style='color:red;'>Błąd podczas usuwania!</p>";
        }

    }
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styleadmin.css">
    <title>Zarządzanie stronami</title>
</head>
<body>
    <header>
        <h1> Panel zarządzania podstronami </h1>

        <nav class="navbar">
            <ul>
                <li> <a href="adminPagesManagment.php">Panel zarządzania podstronami</a></li>
                <li> <a href="adminPagesManagment.php?action=add">Dodaj nową podstronę</a> </li>
                <li> <a href="adminDashBoard.php?">Panel główny</a></li>
            </ul>
        </nav>
     </header>

    <?php

        $action = $_GET['action'] ?? 'list';
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        switch ($action) {
            case 'edit':
                echo EdytujPodstrone($mysqli, $id);
                break;

            case 'delete':
                UsunPodstrone($mysqli, $id);
                echo ListaPodstron($mysqli);
                break;

            case 'add':
                echo DodajPodstrone($mysqli);
                break;

            default:
                echo ListaPodstron($mysqli);
        }
    ?>

</body>
</html>