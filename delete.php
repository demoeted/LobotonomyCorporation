<?php
require('connect.php');

session_start();

    if($_GET && $_GET['id']){
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        $query = "DELETE FROM article WHERE id = :id LIMIT 1";

        $statement = $db->prepare($query);

        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        $statement->execute();

        header('Location: index.php');
        exit;
    }
?>