<?php

    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
    include('cfg.php');
    include('showpage.php');
    $page_id = $_GET['id'] ?? 1; 
    $treść = PokazPodstrone($page_id);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Projekt 1">
    <meta name="keywords" content="HTML5, CSS3, JavaScript">
    <meta name="author" content="Paulina Jurewicz">
    <title>Hodowla aksolotli</title>
    <link rel="stylesheet" href="css\style.css">
    <script src="js/kolorujtlo.js" defer></script>
    <script src="js/timedate.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>

    <header>
        <h1><a href="index.html">Aksolotle</a></h1>
        <nav class="navbar">
            <ul>
                <li><a href="index.php?idp=glowna">Strona główna</a></li>
                <li><a href="index.php?idp=hodowla">Hodowla</a></li>
                <li><a href="index.php?idp=odżywianie">Odżywianie</a></li>
                <li><a href="index.php?idp=zdrowie">Zdrowie i pielęgnacja</a></li>
                <li><a href="index.php?idp=ciekawostki">Ciekawostki</a></li>
                <li><a href="index.php?idp=galeria">Galeria</a></li>
                <li><a href="index.php?idp=filmy">Filmy</a></li>

            </ul>
        </nav>

    </header>
    

    <main>
        <h5>Wybierz kolor tła:</h5>
        <div id="colorButtons">
            <div class="buttons-div">
                <button data-color="#4a52ba" class="buttons">ciemnoniebieski</button>
                <button data-color="#03e8fc" class="buttons">jasnoniebieski</button>
                <button data-color="#3a8a47" class="buttons">zielony</button>
                <button data-color="#03fc9d" class="buttons">miętowy</button>
                <button data-color="#6d60a3" class="buttons">fioletowy</button>
                <button data-color="#c0c0c0" class="buttons">szary</button>
            </div>
        </div>
        <?php
            include('showpage.php');

        ?>
        <div class="time-div">
        <p id="data" class="czas"></p>
        <p id="zegarek" class="czas"></p>
    </div>
        <?php
             echo $treść; 
        ?>
    </main>
    

    
    <footer>
        <hr>
         &copy; 2025 Strona o aksolotlach </br>
         Autor: Paulina Jurewicz 
    </footer>


    <?php
        $nr_indeksu = '174998';
        $nrGrupy = 'ISI2';

        echo 'Autor: Paulina Jurewicz ' . $nr_indeksu . ' grupa ' . $nrGrupy . '<br>';
    ?>

</body>
</html>