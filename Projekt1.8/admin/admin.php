<?php
    // Połączenie z bazą danych MySQL
    $link = new mysqli("localhost", "root", "", "moja_strona");

    if ($link->connect_errno) {
        die("Błąd połączenia z bazą danych!");
    }

    // obsługuje funkcje kontaktu i przypominania hasła
    include_once "../contact.php";

    // wysyła maila z hasłem
    if (isset($_GET['przypomnij'])) {
        PrzypomnijHaslo("jurewiczp2@domena.pl");
    }

    //Funkcja wyświetlająca liczbę podstron
    function ListaPodstron($link)
    {
        $sql = "SELECT * FROM page_list ORDER BY id DESC";
        $result = $link->query($sql);

        $html = "<h2>Lista podstron</h2>";
        $html .= "<table border='1' cellpadding='5' cellspacing='0'>
                    <tr>
                        <th>ID</th>
                        <th>Tytuł</th>
                        <th>Status</th>
                        <th>Akcje</th>
                    </tr>";

        while ($row = $result->fetch_assoc()) {
            $status = $row['status'] == 1 ? "Aktywna" : "Nieaktywna";

            $html .= "
                <tr>
                    <td>{$row['id']}</td>
                    <td>{$row['page_title']}</td>
                    <td>$status</td>
                    <td>
                        <a href='admin.php?edit={$row['id']}'>Edytuj</a> |
                        <a href='admin.php?delete={$row['id']}' onclick='return confirm(\"Usunąć podstronę?\")'>
                            Usuń
                        </a>
                    </td>
                </tr>";
        }

        $html .= "</table>";

        return $html;
    }

    //Funkcja umożliwiająca edycje podstron przez formularz
    function EdytujPodstrone($link, $id)
    {
        $id = intval($id);
        $sql = "SELECT * FROM page_list WHERE id = $id LIMIT 1";
        $result = $link->query($sql);

        if ($result->num_rows == 0) {
            return "<p>Nie znaleziono podstrony.</p>";
        }

        $row = $result->fetch_assoc();
        $checked = $row['status'] == 1 ? "checked" : "";

        if (isset($_POST['save_page'])) {

            $title = $link->real_escape_string($_POST['title']);
            $content = $link->real_escape_string($_POST['content']);
            $status = isset($_POST['status']) ? 1 : 0;

            $update = "
                UPDATE page_list
                SET page_title='$title', page_content='$content', status='$status'
                WHERE id=$id
            ";

            if ($link->query($update)) {
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
                        <input type='checkbox' name='status' $checked>
                        Strona aktywna
                    </label>
                </p>

                <p>
                    <input type='submit' name='save_page' value='Zapisz zmiany'>
                </p>
            </form>
        ";
    }

    //Funkcja umożliwiająca dodanie nowej podstrony przez formularz
    function DodajNowaPodstrone($link)
    {
        if (isset($_POST['add_page'])) {

            $title = $link->real_escape_string($_POST['title']);
            $content = $link->real_escape_string($_POST['content']);
            $status = isset($_POST['status']) ? 1 : 0;

            $sql = "
                INSERT INTO page_list (page_title, page_content, status)
                VALUES ('$title', '$content', '$status')
            ";

            if ($link->query($sql)) {
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

    //Funkcja która umożliwia usunięcie podstrony
    function UsunPodstrone($link, $id)
    {
        $id = intval($id);

        $sql = "DELETE FROM page_list WHERE id = $id LIMIT 1";

        if ($link->query($sql)) {
            return "<p style='color:green;'>Usunięto podstronę o ID $id.</p>";
        } else {
            return "<p style='color:red;'>Błąd podczas usuwania.</p>";
        }
    }

?>

<!-- Interfejs admina -->
<h1>Panel administracyjny</h1>
<p>
    <a href="admin.php">Lista podstron</a> |
    <a href="admin.php?add=1">Dodaj nową podstronę</a>
</p>
<hr>

<?php

    //router panelu admina

    if (isset($_GET['edit'])) {
        echo EdytujPodstrone($link, $_GET['edit']);
    } 
    elseif (isset($_GET['delete'])) {
        echo UsunPodstrone($link, $_GET['delete']);
        echo ListaPodstron($link);
    }
    elseif (isset($_GET['add'])) {
        echo DodajNowaPodstrone($link);
    }
    else {
        echo ListaPodstron($link);
    }
?>
