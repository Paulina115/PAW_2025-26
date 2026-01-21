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

    //Dane do testów kontakt i przypomnij hasło
    $admin_email = "jurewiczp2@gmail.com";
    $kontakt_email = "jurewiczp2@gmail.com";
    
    //Połączenie z bazą
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $baza);

    if ($mysqli->connect_error) {
        die('<b>Przerwane połączenie:</b> ' . $mysqli->connect_error);
    } 

    $mysqli->set_charset("utf8mb4");

?>