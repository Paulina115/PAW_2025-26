<!-- Moduł implementujący panel zarządzania produktami -->

<?php
    require_once("../cfg.php");
    if (!isset($_SESSION['admin'])) {
        header('Location: login.php');
        exit;
    }

    // Funkcja do dodawania kategorii
    function DodajKategorie(mysqli $mysqli, $nazwa, $matka = 0) {
        $nazwa = $mysqli->real_escape_string($nazwa);
        $matka = (int)$matka;
        $mysqli->query("INSERT INTO kategorie (nazwa, matka) VALUES ('$nazwa', $matka)");
    }

    //Funkcja do edytowania kategorii
    function EdytujKategorie(mysqli $mysqli, $id, $nazwa, $matka = 0) {
        $id = (int)$id;
        $nazwa = $mysqli->real_escape_string($nazwa);
        $matka = (int)$matka;
        $mysqli->query("UPDATE kategorie SET nazwa='$nazwa', matka=$matka WHERE id=$id");
    }

    //Funkcja do usuwania kategorii
    function UsunKategorie(mysqli $mysqli, $id) {
        $id = (int)$id;
        $mysqli->query("DELETE FROM kategorie WHERE matka=$id"); // usuwa podkategorie
        $mysqli->query("DELETE FROM kategorie WHERE id=$id");    // usuwa kategorię
    }

    //Funkcja do wyświetlania drzewa kategorii
    function PokazKategorie(mysqli $mysqli) {
        $html = "<h2>Kategorie</h2>
            <ul>";
        $res = $mysqli->query("SELECT * FROM kategorie WHERE matka=0 ORDER BY id ASC");
        while($row = $res->fetch_assoc()) {
            $html .= "<li>{$row['nazwa']} 
                        <a href='?action=edit_category&id={$row['id']}'>Edytuj</a> | 
                        <a href='?action=delete_category&id={$row['id']}' onclick='return confirm(\"Usunąć kategorię?\")'>Usuń</a>";
            $sub = $mysqli->query("SELECT * FROM kategorie WHERE matka={$row['id']} ORDER BY id ASC");
            if($sub->num_rows>0){
                $html .= "<ul>";
                while($s = $sub->fetch_assoc()){
                    $html .= "<li>{$s['nazwa']} 
                                <a href='?action=edit_category&id={$s['id']}'>Edytuj</a> | 
                                <a href='?action=delete_category&id={$s['id']}' onclick='return confirm(\"Usunąć kategorię?\")'>Usuń</a>
                            </li>";
                }
                $html .= "</ul>";
            }
            $html .= "</li>";
        }
        $html .= "</ul>";
        return $html;
    }


    function KategorieSelect(mysqli $mysqli, $selected_id=0) {
        $html = '';
        $res = $mysqli->query("SELECT * FROM kategorie ORDER BY matka, nazwa ASC");
        while($row = $res->fetch_assoc()){
            $selected = $row['id']==$selected_id ? 'selected' : '';
            $prefix = $row['matka']!=0 ? '-- ' : '';
            $html .= "<option value='{$row['id']}' $selected>$prefix{$row['nazwa']}</option>";
        }
        return $html;
    }

    // Formukarz dodawania kategorii
    function FormularzKategorie($action='add', $id=0, $nazwa='', $matka=0){
        $submit = $action==='add' ? 'Dodaj kategorię' : 'Zapisz zmiany';
        return "
        <h2>".($action==='add'?'Dodaj nową kategorię':'Edytuj kategorię')."</h2>
        <form method='post'>
            <input type='hidden' name='id' value='$id'>
            <label>Nazwa:<br><input type='text' name='nazwa' value='".htmlspecialchars($nazwa)."' required></label><br>
            <label>Podkategoria do:<br><input type='number' name='matka' value='".(int)$matka."'></label><br>
            <input type='submit' name='save_category' value='$submit'>
        </form>";
    }

    // Funkcja do dodawania produktu
    function DodajProdukt(mysqli $mysqli){
        if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['dodaj_produkt'])){
            $tytul = $mysqli->real_escape_string($_POST['tytul']);
            $opis = $mysqli->real_escape_string($_POST['opis']);
            $cena_netto = (float)$_POST['cena_netto'];
            $podatek_vat = (int)$_POST['podatek_vat'];
            $ilosc_sztuk = (int)$_POST['ilosc_sztuk'];
            $status = isset($_POST['status']) ? 1 : 0;
            $kategoria = (int)$_POST['kategoria'];
            $gabaryt = $mysqli->real_escape_string($_POST['gabaryt_produktu']);
            $zdjecie = $mysqli->real_escape_string($_POST['zdjecie']);

            $mysqli->query("INSERT INTO produkty
                (tytul, opis, data_utworzenia, cena_netto, podatek_vat, ilosc_sztuk, status, kategoria, gabaryt_produktu, zdjecie)
                VALUES ('$tytul','$opis',NOW(),'$cena_netto','$podatek_vat','$ilosc_sztuk','$status','$kategoria','$gabaryt','$zdjecie')");
            echo "<p>Produkt dodany!</p>";
        }

        return "
        <h2>Dodaj produkt</h2>
        <form method='post'>
            <input type='text' name='tytul' placeholder='Tytuł' required><br>
            <textarea name='opis' placeholder='Opis' required></textarea><br>
            <input name='cena_netto' placeholder='Cena netto' type='number' step='0.01' required><br>
            <input name='podatek_vat' placeholder='VAT %' type='number' required><br>
            <input name='ilosc_sztuk' placeholder='Ilość' type='number' required><br>
            <input type='text' name='gabaryt_produktu' placeholder='Gabaryt'><br>
            <input type='text' name='zdjecie' placeholder='Link do zdjęcia'><br>
            <select name='kategoria'>".KategorieSelect($mysqli)."</select><br>
            <label><input type='checkbox' name='status'> Dostępny</label><br>
            <input type='submit' name='dodaj_produkt' value='Dodaj produkt'>
        </form>";
    }

    //Funkcja do edytowania produktu
    function EdytujProdukt(mysqli $mysqli, $id){
        if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['edytuj_produkt'])){
            $tytul = $mysqli->real_escape_string($_POST['tytul']);
            $opis = $mysqli->real_escape_string($_POST['opis']);
            $cena_netto = (float)$_POST['cena_netto'];
            $podatek_vat = (int)$_POST['podatek_vat'];
            $ilosc_sztuk = (int)$_POST['ilosc_sztuk'];
            $status = isset($_POST['status']) ? 1 : 0;
            $kategoria = (int)$_POST['kategoria'];
            $gabaryt = $mysqli->real_escape_string($_POST['gabaryt_produktu']);
            $zdjecie = $mysqli->real_escape_string($_POST['zdjecie']);

            $mysqli->query("UPDATE produkty SET 
                tytul='$tytul', opis='$opis', cena_netto='$cena_netto', podatek_vat='$podatek_vat',
                ilosc_sztuk='$ilosc_sztuk', status='$status', kategoria='$kategoria', gabaryt_produktu='$gabaryt',
                zdjecie='$zdjecie'
                WHERE id=$id");
            echo "<p>Produkt zaktualizowany!</p>";
        }

        $res = $mysqli->query("SELECT * FROM produkty WHERE id=$id LIMIT 1")->fetch_assoc();
        if(!$res) return "<p>Nie znaleziono produktu.</p>";

        return "
        <h2>Edytuj produkt</h2>
        <form method='post'>
            <input type='hidden' name='id' value='$id'>
            <input name='tytul' value='".htmlspecialchars($res['tytul'])."' required><br>
            <textarea name='opis' required>".htmlspecialchars($res['opis'])."</textarea><br>
            <input name='cena_netto' value='{$res['cena_netto']}' type='number' step='0.01' required><br>
            <input name='podatek_vat' value='{$res['podatek_vat']}' type='number' required><br>
            <input name='ilosc_sztuk' value='{$res['ilosc_sztuk']}' type='number' required><br>
            <input name='gabaryt_produktu' value='".htmlspecialchars($res['gabaryt_produktu'])."'><br>
            <input name='zdjecie' value='".htmlspecialchars($res['zdjecie'])."'><br>
            <select name='kategoria'>".KategorieSelect($mysqli, $res['kategoria'])."</select><br>
            <label><input type='checkbox' name='status' ".($res['status']?'checked':'')."> Dostępny</label><br>
            <input type='submit' name='edytuj_produkt' value='Zapisz zmiany'>
        </form>";
    }

    //Funkcja do wyświetlania listy produktów
    function PokazProdukty(mysqli $mysqli){
        $html = "<h2>Produkty</h2><table class='admin-table'>
            <tr><th>ID</th><th>Tytuł</th><th>Cena</th><th>Ilość</th><th>Status</th><th>Akcje</th></tr>";
        $res = $mysqli->query("SELECT * FROM produkty ORDER BY id DESC");
        while($p = $res->fetch_assoc()){
            $dostepny = $p['status']==1 && $p['ilosc_sztuk']>0;
            $html .= "<tr>
                <td>{$p['id']}</td>
                <td>{$p['tytul']}</td>
                <td>{$p['cena_netto']} zł</td>
                <td>{$p['ilosc_sztuk']}</td>
                <td>".($dostepny?'Dostępny':'Niedostępny')."</td>
                <td>
                    <a href='?action=edit_product&id={$p['id']}'>Edytuj</a> | 
                    <a href='?action=delete_product&id={$p['id']}' onclick='return confirm(\"Usunąć produkt?\")'>Usuń</a>
                </td>
            </tr>";
        }
        $html .= "</table>";
        return $html;
    }

    //Funkcja do usuwania produktu
    function UsunProdukt(mysqli $mysqli, $id){
        $id = (int)$id;
        $mysqli->query("DELETE FROM produkty WHERE id=$id LIMIT 1");
    }

    ?>
    <!DOCTYPE html>
    <html lang="pl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../css/styleadmin.css">
        <title>Panel zarządzania produktami</title>
    </head>
    <body>
    <header>
        <h1>Panel zarządzania produktami</h1>
        <nav class="navbar">
            <ul>
                <li><a href="?action=list_categories">Kategorie</a></li>
                <li><a href="?action=add_category">Dodaj kategorię</a></li>
                <li><a href="?action=list_products">Produkty</a></li>
                <li><a href="?action=add_product">Dodaj produkt</a></li>
                <li><a href="adminDashBoard.php">Panel główny</a></li>
            </ul>
        </nav>
    </header>

    <div class="content">
    <?php

    $action = $_GET['action'] ?? 'list_categories';
    $id = (int)($_GET['id'] ?? 0);

    if($_SERVER['REQUEST_METHOD']==='POST'){
        if(isset($_POST['save_category'])){
            if($action==='add_category') DodajKategorie($mysqli, $_POST['nazwa'], $_POST['matka']);
            if($action==='edit_category') EdytujKategorie($mysqli, $id, $_POST['nazwa'], $_POST['matka']);
        }
    }

    if($action==='delete_category'){ UsunKategorie($mysqli,$id); $action='list_categories'; }
    if($action==='delete_product'){ UsunProdukt($mysqli,$id); $action='list_products'; }

    switch($action){
        case 'list_categories': 
            echo PokazKategorie($mysqli); 
            break;
        case 'add_category': 
            echo FormularzKategorie('add');
            break;
        case 'edit_category': 
            $row = $mysqli->query("SELECT * FROM kategorie WHERE id=$id LIMIT 1")->fetch_assoc();
            echo $row ? FormularzKategorie('edit', $id, $row['nazwa'], $row['matka']) : "<p>Nie znaleziono kategorii.</p>";
            break;
        case 'list_products': 
            echo PokazProdukty($mysqli); 
            break;
        case 'add_product': 
            echo DodajProdukt($mysqli); 
            break;
        case 'edit_product': 
            echo EdytujProdukt($mysqli, $id); 
            break;
    }
?>
</div>
</body>
</html>
