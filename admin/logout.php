<!-- Moduł implemetujący wylogowanie się admina. -->

<?php
    require_once '../cfg.php';
    session_destroy();
    header('Location: login.php');
    exit;

?>