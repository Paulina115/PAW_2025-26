<?php
require_once("cfg.php");

if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

//Funckjad do dodawania produktów do koszyka
function addToCart($id, $qty = 1){
    if(isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] += $qty;
    } else {
        $_SESSION['cart'][$id] = $qty;
    }
}

//Funkcja dp usuwania produktów z koszyka
function removeFromCart($id){
    unset($_SESSION['cart'][$id]);
}

//Funkcja do aktualizacji produktów w koszyku
function updateQty($id, $qty){
    if($qty > 0) $_SESSION['cart'][$id] = $qty;
    else unset($_SESSION['cart'][$id]);
}


if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['add_id'])) addToCart((int)$_POST['add_id'], (int)$_POST['qty']);
    if(isset($_POST['update_id'])) updateQty((int)$_POST['update_id'], (int)$_POST['qty']);
    header("Location: index.php?id=11");
    exit;
}

if(isset($_GET['remove'])){
    removeFromCart((int)$_GET['remove']);
    header("Location: index.php?id=11");
    exit;
}

//Funkcja wyswietlająca koszyk
function showCart($mysqli){
    if(empty($_SESSION['cart'])) return "<p>Twój koszyk jest pusty.</p>";

    $ids = implode(',', array_keys($_SESSION['cart']));
    $res = $mysqli->query("SELECT * FROM produkty WHERE id IN($ids)");
    $total = 0;

    $html = "<table class='cart-table'>
        <tr>
            <th>Produkt</th>
            <th>Cena brutto</th>
            <th>Ilość</th>
            <th>Wartość</th>
            <th>Akcje</th>
        </tr>";

    while($p = $res->fetch_assoc()){
        $qty = $_SESSION['cart'][$p['id']];
        $gross = $p['cena_netto'] * (1 + $p['podatek_vat']/100);
        $value = $gross * $qty;
        $total += $value;

        $html .= "<tr>
            <td>".htmlspecialchars($p['tytul'])."</td>
            <td>".number_format($gross,2)." zł</td>
            <td>
                <form method='post' style='display:inline'>
                    <input type='number' name='qty' value='$qty' min='1'>
                    <input type='hidden' name='update_id' value='{$p['id']}'>
                    <input type='submit' value='Zmień'>
                </form>
            </td>
            <td>".number_format($value,2)." zł</td>
            <td><a href='index.php?id=11&remove={$p['id']}' class='remove-btn'>Usuń</a></td>
        </tr>";
    }

    $html .= "<tr>
        <td colspan='3' align='right'><strong>Razem:</strong></td>
        <td colspan='2'><strong>".number_format($total,2)." zł</strong></td>
    </tr>";
    $html .= "</table>";

    return $html;
}
?>

<!-- STRONA SKLEPU -->
<div class="shop-page">
    <h2>Produkty w sklepie</h2>

    <!-- Lista produktów -->
    <ul>
        <?php
        $res = $mysqli->query("SELECT * FROM produkty WHERE status=1 ORDER BY id ASC");
        while($p = $res->fetch_assoc()): ?>
            <li>
                <strong><?php echo htmlspecialchars($p['tytul']); ?></strong>
                <br>
                <?php echo number_format($p['cena_netto'],2)." zł + ".$p['podatek_vat']."% VAT"; ?>
                <form method="post" style="margin-top:5px;">
                    <input type="hidden" name="add_id" value="<?php echo $p['id']; ?>">
                    <input type="number" name="qty" value="1" min="1">
                    <input type="submit" value="Dodaj do koszyka">
                </form>
            </li>
        <?php endwhile; ?>
    </ul>

    <!-- Koszyk -->
    <h2>Twój koszyk</h2>
    <?php echo showCart($mysqli); ?>
</div>
