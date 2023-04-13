<?php
    session_start();
    require('connect.php');

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $article_id = filter_input(INPUT_GET, 'article_id', FILTER_VALIDATE_INT);
    $private = 1;

    $query = "UPDATE comment SET private = :private WHERE id = :id LIMIT 1";

    $statement = $db->prepare($query);

    $statement->bindValue(':private', $private);
    $statement->bindValue(':id', $id);

    $statement->execute();

    header("Location: article.php?id=$article_id");
    exit();
?>