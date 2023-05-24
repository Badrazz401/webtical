<?php
// Check if the form has been submitted
if (isset($_POST['ok'])) {
    require("./config/connexion.php");

    // Get the post data from the form
    $content = $_POST['content'];
    $username = $_POST['username'];
    $image = null;

    // Check if an image was uploaded
    if (isset($_FILES['image'])) {
        $image = $_FILES['image']['name'];
        $image_temp = $_FILES['image']['tmp_name'];
        move_uploaded_file($image_temp, 'uploads/' . $image);
    }


    // Insert the post data into the database
    try {
        $insert_post = $db->prepare('INSERT INTO publication (username, contenuPub, image) VALUES (:username, :contenuPub, :image)');
        $insert_post->bindParam(':username', $username);
        $insert_post->bindParam(':contenuPub', $content);
        $insert_post->bindParam(':image', $image);
        $insert_post->execute();
        header('Location: home.php');
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}