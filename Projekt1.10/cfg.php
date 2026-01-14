<?php
    session_start();

    // Dane logowania dla panelu admina
    $login = "admin";
    $pass = "3r7hdb28100spa";

    //Dane do połączenia z bazą
    $dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
    $baza = 'moja_strona';
    
    //Połączenie z bazą
    $link = new mysqli($dbhost, $dbuser, $dbpass, $baza);

    if ($link->connect_error) {
        die('<b>Przerwane połączenie:</b> ' . $link->connect_error);
    } 
    $link->set_charset("utf8mb4");
?>