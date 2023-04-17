<?php
require('connect.php');

session_start();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if($id){
        $query = "SELECT path FROM image WHERE article = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        $imagesToDelete = $statement->fetchAll();

        if(isset($imagesToDelete) && !empty($imagesToDelete)){
            foreach($imagesToDelete as $imageToDelete){
                unlink($imageToDelete['path']);
            }

            $query = "DELETE FROM image WHERE article_id = :article_id";

            $statement = $db->prepare($query);
            $statement->bindValue(':article_id', $id, PDO::PARAM_INT);
            $statement->execute();
        }

        $query = "DELETE FROM article WHERE article_id = :article_id LIMIT 1";

        $statement = $db->prepare($query);
        $statement->bindValue(':article_id', $id, PDO::PARAM_INT);
        $statement->execute();

        header('Location: index.php');
        exit;
    }
?>