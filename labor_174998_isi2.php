<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="labor_174998_isi2.php" method="get">
        <label>username_get: </label><br>
        <input type = "text" name="username_get"><br>
        <label>password_get:</label><br>
        <input type="password" name="password_get"><br>
        <input type="submit" value="log in">
    </form>
    <form action="labor_174998_isi2.php" method="post">
        <label>username_post: </label><br>
        <input type = "text" name="username_post"><br>
        <label>password_post:</label><br>
        <input type="password" name="password_post"><br>
        <input type="submit" value="log in">
    </form>
</body>
</html>
<?php
    $nr_indeksu = '174998';
    $nrGrupy = 'isi2';

    echo 'Paulina Jurewicz ' .$nr_indeksu.' grupa ' .$nrGrupy. '<br>';

    echo'<br> a) Zastosowanie metody include() <br>';
    include("header.php");
    echo'<br> a) Zastosowanie metody require_once() <br>';
    require_once("funkcja.php");
    require_once("funkcja.php");
    hello("Anna");
    echo'<br> b) Zastosowanie warunków if,else,elseif <br>';
    $age = 55;
    if($age < 18){
        echo "<br> Jesteś niepełnoletni <br>";
    }
    elseif($age < 1){
        echo "<br> Niepoprawny wiek <br>";
    }
    else{
        echo "<br> Jesteś pełnoletni <br>";
    }
    echo'<br> b) Zastosowanie switch <br>';
    switch($age){
        case $age < 18:
            echo "<br> dziecko <br>";
            break;
        case 18 <= $age && $age <= 40:
            echo "<br>młody dorosły<br>";
            break;
        case 40 < $age  && $age<= 55:
            echo "<br>wiek średni<br>";
            break;
        case 55 < $age  && $age<= 70 :
            echo "<br>późna dorosłość<br>";
            break;
        case $age > 70:
            echo "<br>starość<br>";
            break;
        default:
            echo "<br>Nie poprawny wiek<br>";

    }
    echo'<br> c) Zastosowanie pętli while() <br>';
    $c = 0;
    while($c < 20){
        $c+=2;
        echo $c . "<br>";
    }
    echo'<br> c) Zastosowanie pętli for() <br>';
    for($i = 10;$i > 0;$i--){
        echo $i . "<br>";
    }
    echo'<br> d) Zmienna $_GET <br>';
    echo $_GET["username_get"];
    echo'<br> d) Zmienna $_POST <br>';
    echo $_POST["username_post"];
    echo'<br> d) Zmienna $_SESSION <br>';
    session_start();

    echo "Witaj, " . $_SESSION['imie'] . "!<br>";


?>