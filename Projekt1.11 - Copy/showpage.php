<!-- Funkcja która umożliwia wyświetlenie podstron
 zapisanych w bazie danych -->

<?php

    function PokazPodstrone(mysqli $mysqli, int $id)
    {
        $id_clear = (int)$id;

        $query = "SELECT page_content FROM page_list WHERE id = $id_clear LIMIT 1";
        $result = $mysqli->query($query);

        if (!$result || $result->num_rows === 0) {
            return '[Nie znaleziono strony]';
        }

        $row = $result->fetch_assoc();
        return $row['page_content'];
        }

?>
