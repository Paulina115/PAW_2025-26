<?php
// Połączenie z bazą
$link = new mysqli("localhost", "root", "", "moja_strona");
if ($link->connect_errno) {
    die("Błąd połączenia z bazą danych");
}

// Kategorie produktów

// lista kategorii do <select>
function ListaKategoriiSelect($link)
{
    $html = "";
    $res = $link->query("SELECT * FROM kategorie WHERE matka=0 ORDER BY nazwa ASC");

    while ($r = $res->fetch_assoc()) {
        $html .= "<option value='{$r['id']}'>{$r['nazwa']}</option>";
    }
    return $html;
}

// drzewo kategorii (matki + dzieci)
function PokazKategorie($link)
{
    $html = "<ul>";
    $matki = $link->query("SELECT * FROM kategorie WHERE matka=0 ORDER BY id ASC");

    while ($m = $matki->fetch_assoc()) {
        $html .= "<li><strong>{$m['nazwa']}</strong>
            <a href='admin.php?kategorie=1&delete={$m['id']}'>Usuń</a>";

        $dzieci = $link->query("SELECT * FROM kategorie WHERE matka={$m['id']}");
        if ($dzieci->num_rows > 0) {
            $html .= "<ul>";
            while ($d = $dzieci->fetch_assoc()) {
                $html .= "<li>{$d['nazwa']}
                    <a href='admin.php?kategorie=1&delete={$d['id']}'>Usuń</a>
                </li>";
            }
            $html .= "</ul>";
        }
        $html .= "</li>";
    }
    return $html . "</ul>";
}

function DodajKategorie($link)
{
    if (isset($_POST['dodaj_kategorie'])) {
        $nazwa = $link->real_escape_string($_POST['nazwa']);
        $matka = intval($_POST['matka']);
        $link->query("INSERT INTO kategoriw (nazwa, matka) VALUES ('$nazwa',$matka)");
    }
}

function UsunKategorie($link, $id)
{
    $id = intval($id);
    $link->query("DELETE FROM kategorie WHERE matka=$id");
    $link->query("DELETE FROM kategorie WHERE id=$id");
}

// Produkty

function DodajProdukt($link)
{
    if (isset($_POST['dodaj_produkt'])) {

        $tytul = $link->real_escape_string($_POST['tytul']);
        $opis = $link->real_escape_string($_POST['opis']);
        $cena_netto = floatval($_POST['cena_netto']);
        $podatek_vat = intval($_POST['podatek_vat']);
        $ilosc_sztuk = intval($_POST['ilosc_sztuk']);
        $status = isset($_POST['status']) ? 1 : 0;
        $kategoria = intval($_POST['kategoria']);
        $gabaryt = $link->real_escape_string($_POST['gabaryt_produktu']);
        $zdjecie = $link->real_escape_string($_POST['zdjecie']);

        $sql = "
        INSERT INTO produkty
        (tytul, opis, data_utworzenia, cena_netto, podatek_vat, ilosc_sztuk, status, kategoria, gabaryt_produktu, zdjecie)
        VALUES
        ('$tytul','$opis',NOW(),'$cena_netto','$podatek_vat','$ilosc_sztuk','$status','$kategoria','$gabaryt','$zdjecie')
        ";

        $link->query($sql);
    }

    return "
    <h2>Dodaj produkt</h2>
    <form method='post'>
        <input name='tytul' placeholder='Tytuł'><br>
        <textarea name='opis' placeholder='Opis'></textarea><br>
        <input name='cena_netto' placeholder='Cena netto'><br>
        <input name='podatek_vat' placeholder='VAT %'><br>
        <input name='ilosc_sztuk' placeholder='Ilość'><br>
        <input name='gabaryt_produktu' placeholder='Gabaryt'><br>
        <input name='zdjecie' placeholder='Link do zdjęcia'><br>

        <select name='kategoria'>
            ".ListaKategoriiSelect($link)."
        </select><br>

        <label>
            <input type='checkbox' name='status'> Dostępny
        </label><br>

        <input type='submit' name='dodaj_produkt' value='Dodaj produkt'>
    </form>";
}

function PokazProdukty($link)
{
    $res = $link->query("SELECT * FROM produkty ORDER BY id DESC");

    $html = "<h2>Produkty</h2>
    <table border='1'>
        <tr>
            <th>ID</th>
            <th>Tytuł</th>
            <th>Cena</th>
            <th>Ilość</th>
            <th>Status</th>
            <th>Akcje</th>
        </tr>";

    while ($p = $res->fetch_assoc()) {

        $dostepny =
            $p['status'] == 1 &&
            $p['ilosc_sztuk'] > 0 &&
            (empty($p['data_wygasniecia']) || strtotime($p['data_wygasniecia']) > time());

        $html .= "
        <tr>
            <td>{$p['id']}</td>
            <td>{$p['tytul']}</td>
            <td>{$p['cena_netto']} zł</td>
            <td>{$p['ilosc_sztuk']}</td>
            <td>".($dostepny ? "Dostępny" : "Niedostępny")."</td>
            <td>
                <a href='admin.php?produkty=1&usun={$p['id']}'>Usuń</a>
            </td>
        </tr>";
    }

    return $html."</table>";
}

function UsunProdukt($link, $id)
{
    $id = intval($id);
    $link->query("DELETE FROM produkty WHERE id=$id LIMIT 1");
}

// Interfejs
?>

<h1>Panel administracyjny</h1>
<p>
    <a href="admin.php">CMS</a> |
    <a href="admin.php?kategorie=1">Kategorie</a> |
    <a href="admin.php?produkty=1">Produkty</a>
</p>
<hr>

<?php
// Router

// Kategorie
if (isset($_GET['kategorie'])) {

    if (isset($_GET['delete'])) UsunKategorie($link, $_GET['delete']);
    DodajKategorie($link);

    echo "
    <h2>Dodaj kategorię</h2>
    <form method='post'>
        <input name='nazwa' placeholder='Nazwa'>
        <select name='matka'>
            <option value='0'>Kategoria główna</option>
            ".ListaKategoriiSelect($link)."
        </select>
        <input type='submit' name='dodaj_kategorie' value='Dodaj'>
    </form>";

    echo "<h2>Lista kategorii</h2>";
    echo PokazKategorie($link);
}

// Produkty
elseif (isset($_GET['produkty'])) {

    if (isset($_GET['usun'])) UsunProdukt($link, $_GET['usun']);

    if (isset($_GET['dodaj'])) {
        echo DodajProdukt($link);
    } else {
        echo "<a href='admin.php?produkty=1&dodaj=1'>Dodaj produkt</a><br><br>";
        echo PokazProdukty($link);
    }
}

// Domyślne
else {
    echo "<p>Wybierz moduł z menu powyżej.</p>";
}
?>
