<?php
    if(!function_exists('PokazPodstrone')){
        function PokazPodstrone($id)
        {
            global $link;

            $id_clear = mysqli_real_escape_string($link, $id);

            $query = "SELECT * FROM page_list WHERE id = '$id_clear' LIMIT 1";
            $result = $link->query($query);

            if ($result && $row = $result->fetch_assoc()) {
                $web = $row['page_content'];
            } else {
                $web = '[nie_znaleziono_strony]';
            }

            return $web;
        }
    }
?>
