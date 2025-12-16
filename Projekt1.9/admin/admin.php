<?php
// Połączenie z bazą danych MySQL
$link = new mysqli("localhost", "root", "", "moja_strona");
if ($link->connect_errno) {
    die("Błąd połączenia z bazą danych!");
}

// Obsługuje funkcje kontaktu i przypominania hasła
include_once "../contact.php";

// Wysyła maila z hasłem, jeśli kliknięto przypomnienie
if (isset($_GET['przypomnij'])) {
    PrzypomnijHaslo("jurewiczp2@domena.pl");
}

// funkcje podstron cms


// Funkcja wyświetlająca listę podstron
function ListaPodstron($link) {
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
                    <a href='admin.php?delete={$row['id']}' onclick='return confirm(\"Usunąć podstronę?\")'>Usuń</a>
                </td>
            </tr>";
    }

    $html .= "</table>";
    return $html;
}

// Funkcja do edycji podstrony
function EdytujPodstrone($link, $id) {
    $id = intval($id);
    $sql = "SELECT * FROM page_list WHERE id = $id LIMIT 1";
    $result = $link->query($sql);

    if ($result->num_rows == 0) return "<p>Nie znaleziono podstrony.</p>";

    $row = $result->fetch_assoc();
    $checked = $row['status'] == 1 ? "checked" : "";

    // Obsługa zapisu po przesłaniu formularza
    if (isset($_POST['save_page'])) {
        $title = $link->real_escape_string($_POST['title']);
        $content = $link->real_escape_string($_POST['content']);
        $status = isset($_POST['status']) ? 1 : 0;

        $update = "UPDATE page_list SET page_title='$title', page_content='$content', status='$status' WHERE id=$id";
        if ($link->query($update)) echo "<p style='color:green;'>Zapisano zmiany!</p>";
        else echo "<p style='color:red;'>Błąd podczas zapisu!</p>";
    }

    // Formularz edycji podstrony
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
                    <input type='checkbox' name='status' $checked> Strona aktywna
                </label>
            </p>
            <p><input type='submit' name='save_page' value='Zapisz zmiany'></p>
        </form>
    ";
}

// Funkcja dodawania nowej podstrony
function DodajNowaPodstrone($link) {
    if (isset($_POST['add_page'])) {
        $title = $link->real_escape_string($_POST['title']);
        $content = $link->real_escape_string($_POST['content']);
        $status = isset($_POST['status']) ? 1 : 0;

        $sql = "INSERT INTO page_list (page_title, page_content, status) VALUES ('$title', '$content', '$status')";
        if ($link->query($sql)) echo "<p style='color:green;'>Dodano nową podstronę!</p>";
        else echo "<p style='color:red;'>Błąd podczas dodawania!</p>";
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
                    <input type='checkbox' name='status'> Strona aktywna
                </label>
            </p>
            <p><input type='submit' name='add_page' value='Dodaj podstronę'></p>
        </form>
    ";
}

// Funkcja usuwania podstrony
function UsunPodstrone($link, $id) {
    $id = intval($id);
    $sql = "DELETE FROM page_list WHERE id = $id LIMIT 1";
    if ($link->query($sql)) return "<p style='color:green;'>Usunięto podstronę o ID $id.</p>";
    else return "<p style='color:red;'>Błąd podczas usuwania.</p>";
}

// funkcje kategorii produktów

// Dodawanie kategorii
function DodajKategorie($link, $nazwa, $matka = 0) {
    $nazwa = $link->real_escape_string($nazwa);
    $matka = intval($matka);
    $link->query("INSERT INTO categories (nazwa, matka) VALUES ('$nazwa', $matka)");
}

// Edycja kategorii
function EdytujKategorie($link, $id, $nazwa, $matka = 0) {
    $id = intval($id);
    $nazwa = $link->real_escape_string($nazwa);
    $matka = intval($matka);
    $link->query("UPDATE categories SET nazwa='$nazwa', matka=$matka WHERE id=$id");
}

// Usuwanie kategorii (razem z podkategoriami)
function UsunKategorie($link, $id) {
    $id = intval($id);
    $link->query("DELETE FROM categories WHERE matka=$id"); // usuwa podkategorie
    $link->query("DELETE FROM categories WHERE id=$id");    // usuwa kategorię
}

// Wyświetlanie kategorii w formie drzewa
function PokazKategorie($link) {
    $html = "<ul>";
    $res = $link->query("SELECT * FROM categories WHERE matka=0 ORDER BY id ASC");
    while ($row = $res->fetch_assoc()) {
        $html .= "<li>{$row['nazwa']} 
                    <a href='?kategorie&edit={$row['id']}'>Edytuj</a> | 
                    <a href='?kategorie&delete={$row['id']}' onclick='return confirm(\"Usunąć kategorię?\")'>Usuń</a>";
        $sub = $link->query("SELECT * FROM categories WHERE matka={$row['id']} ORDER BY id ASC");
        if ($sub->num_rows > 0) {
            $html .= "<ul>";
            while ($s = $sub->fetch_assoc()) {
                $html .= "<li>{$s['nazwa']} 
                            <a href='?kategorie&edit={$s['id']}'>Edytuj</a> | 
                            <a href='?kategorie&delete={$s['id']}' onclick='return confirm(\"Usunąć kategorię?\")'>Usuń</a>
                          </li>";
            }
            $html .= "</ul>";
        }
        $html .= "</li>";
    }
    $html .= "</ul>";
    return $html;
}

// Interfejs admina
?>

<h1>Panel administracyjny</h1>
<p>
    <a href="admin.php">Lista podstron</a> |
    <a href="admin.php?add=1">Dodaj nową podstronę</a> |
    <a href="admin.php?kategorie=1">Kategorie produktów</a>
</p>
<hr>

<?php
// router admina

// Obsługa kategorii
if (isset($_GET['kategorie'])) {
    // Usuń kategorię
    if (isset($_GET['delete'])) UsunKategorie($link, $_GET['delete']);

    // Dodaj kategorię
    if (isset($_POST['add_kategoria'])) DodajKategorie($link, $_POST['nazwa'], $_POST['matka']);

    // Edytuj kategorię
    if (isset($_POST['edit_kategoria'])) EdytujKategorie($link, $_POST['id'], $_POST['nazwa'], $_POST['matka']);

    // Formularz dodawania / edycji
    if (isset($_GET['edit'])) {
        $id = intval($_GET['edit']);
        $res = $link->query("SELECT * FROM categories WHERE id=$id LIMIT 1");
        $row = $res->fetch_assoc();
        echo "<h2>Edytuj kategorię</h2>
              <form method='post'>
                <input type='hidden' name='id' value='{$row['id']}'>
                <input type='text' name='nazwa' value='{$row['nazwa']}'>
                <select name='matka'>
                    <option value='0'>Kategoria główna</option>";
                    $res2 = $link->query("SELECT * FROM categories WHERE matka=0 AND id != {$row['id']}");
                    while($r = $res2->fetch_assoc()){
                        $sel = $r['id']==$row['matka'] ? "selected" : "";
                        echo "<option value='{$r['id']}' $sel>{$r['nazwa']}</option>";
                    }
        echo "</select>
              <input type='submit' name='edit_kategoria' value='Zapisz zmiany'>
              </form>";
    } else {
        echo "<h2>Dodaj nową kategorię</h2>
              <form method='post'>
                <input type='text' name='nazwa' placeholder='Nazwa kategorii'>
                <select name='matka'>
                    <option value='0'>Kategoria główna</option>";
                    $res = $link->query("SELECT * FROM categories WHERE matka=0");
                    while($r = $res->fetch_assoc()){
                        echo "<option value='{$r['id']}'>{$r['nazwa']}</option>";
                    }
        echo "</select>
              <input type='submit' name='add_kategoria' value='Dodaj kategorię'>
              </form>";
    }

    // Lista kategorii
    echo "<h2>Lista kategorii</h2>";
    echo PokazKategorie($link);

    exit(); // kończymy wyświetlanie kategorii
}

// obsługa podstron cms
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
