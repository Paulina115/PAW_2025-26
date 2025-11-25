<?php
    session_start();

    $login = "admin";
    $pass = "3r7hdb28100spa";

    $dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
    $baza = 'moja_strona';

    $link = new mysqli($dbhost, $dbuser, $dbpass, $baza);

    if ($link->connect_error) {
        die('<b>Przerwane połączenie:</b> ' . $link->connect_error);
    } 
    $link->set_charset("utf8mb4");
?>